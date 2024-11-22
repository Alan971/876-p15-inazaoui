<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class MediaController extends AbstractController
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    #[Route("/admin/media", name:"admin_media_index")]
    public function index(Request $request, EntityManagerInterface $em)
    {

        $page = $request->query->getInt('page', 1);

        $criteria = [];

        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        $medias = $em->getRepository(Media::class)->findBy(
            $criteria,
            ['id' => 'ASC'],
            25,
            25 * ($page - 1)
        );
        $total = $em->getRepository(Media::class)->count([]);

        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'page' => $page
        ]);
    }

    #[Route("/admin/media/add", name:"admin_media_add")]
    public function add(Request $request, EntityManagerInterface $em)
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media, ['is_admin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                $media->setUser($this->getUser());
            }
            $file = $form->get('file')->getData();

            if (!$file){
                throw new \Exception('Aucun fichier sélectionné');
            }

            $violations = $this->validator->validate($file, [
                new Assert\File([
                    'mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg'],
                    'maxSize' => '2M',
                ]),
                new Assert\Image([
                    'maxWidth' => 3840, 
                    'maxHeight' => 2160,
                ]),
            ]);
            if (count($violations) > 0) {
                // Si le fichier ne respecte pas les règles de validation, lever une exception
                $errorMessages  = array_map(function($violation){
                    return $violation->getMessage();
                }, iterator_to_array($violations));
                throw new \Exception('Fichier invalide : ' . implode(', ', $errorMessages));
            }

            $uploadDirectory = $this->getParameter('UPLOADS_DIRECTORY');
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($uploadDirectory, $fileName);

            // Mettre à jour le chemin du fichier dans l'entité
            $media->setPath($uploadDirectory . $fileName);
            $em->persist($media);
            $em->flush();

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/admin/media/delete/{id}", name:"admin_media_delete")]
    public function delete(int $id, EntityManagerInterface $em)
    {
        $media = $em->getRepository(Media::class)->find($id);
        $em->remove($media);
        $em->flush();
        unlink($media->getPath());

        return $this->redirectToRoute('admin_media_index');
    }
}