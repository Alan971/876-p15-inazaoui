<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\User;
use App\Form\MediaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class MediaController extends AbstractController
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    #[Route("/admin/media", name:"admin_media_index")]
    public function index(Request $request, EntityManagerInterface $em): Response
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
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaType::class, $media, ['is_admin' => $this->isGranted('ROLE_ADMIN')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                if($this->getUser() instanceof User){
                    $media->setUser($this->getUser());
                }
            }
            /** @var \Symfony\Component\HttpFoundation\File\UploadedFile|null $file */
            $file = $form->get('file')->getData();

            if (!$file){
                throw new \Exception('Aucun fichier sélectionné');
            }

            $violations = $this->validator->validate($file, [
                new Assert\File([
                    'mimeTypes' => ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'],
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
            $extension = $file->guessExtension();
            $fileName = md5(uniqid()) . '.' . $extension;
            if(is_string($uploadDirectory)){
                $file->move($uploadDirectory, $fileName);   
                $media->setPath($uploadDirectory . $fileName);
                $em->persist($media);
                $em->flush();
            }
            else {
                throw new \Exception('Il y a eu un problème lors du téléchargement du fichier');
            }

            return $this->redirectToRoute('admin_media_index');
        }

        return $this->render('admin/media/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/admin/media/delete/{id}", name:"admin_media_delete")]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        /** @var Media $media */
        $media = $em->getRepository(Media::class)->find($id);
        $em->remove($media);
        $em->flush();
        unlink($media->getPath());

        return $this->redirectToRoute('admin_media_index');
    }
}