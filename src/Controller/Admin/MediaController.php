<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;

class MediaController extends AbstractController
{

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
            $media->setPath('uploads/' . md5(uniqid()) . '.' . $media->getFile()->guessExtension());
            $media->getFile()->move('uploads/', $media->getPath());
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