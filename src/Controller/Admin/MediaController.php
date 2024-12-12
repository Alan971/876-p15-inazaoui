<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\User;
use App\Form\MediaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Response;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class MediaController extends AbstractController
{
    #[Route("/admin/media", name:"admin_media_index")]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $page = $request->query->getInt('page', 1);
        $criteria = [];
        $maxMediaPerPage = 25;

        if (!$this->isGranted('ROLE_ADMIN')) {
            $criteria['user'] = $this->getUser();
        }

        $medias = $em->getRepository(Media::class)->findBy(
            $criteria,
            ['id' => 'ASC'],
            $maxMediaPerPage,
            $maxMediaPerPage * ($page - 1)
        );
        $total = $em->getRepository(Media::class)->count([]);

        return $this->render('admin/media/index.html.twig', [
            'medias' => $medias,
            'total' => $total,
            'page' => $page,
            'maxMediaPerPage' => $maxMediaPerPage
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
            try {   
                if ($file){
                    error_log('test 2: ' . $file);
                    $uploadDirectory = $this->getParameter('UPLOADS_DIRECTORY');
                    error_log('test : ' . $file);
                    $extension = $file->guessExtension();
                    error_log('test3 : ' . is_string($uploadDirectory));
                    $fileName = uniqid() . '.' . $extension;
                        if(is_string($uploadDirectory)){
                            error_log('Moving file to: ' . $uploadDirectory . '/' . $fileName);
                            $file->move($uploadDirectory, $fileName);   
                            error_log('File moved successfully.');
                            $media->setPath($uploadDirectory . $fileName);
                            $em->persist($media);
                            $em->flush();
                        }
                        else {
                            throw new \Exception('Le fichier n\'a pas été téléchargé');
                        }

                }
                else {
                    throw new \Exception('Fichier non trouvé');
                    }
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    $e->getMessage()
                );
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