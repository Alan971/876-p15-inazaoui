<?php

namespace App\Controller\Admin;

use App\Entity\Album;
use App\Entity\Media;
use App\Form\AlbumType;
use App\Form\MediaType;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AlbumController extends AbstractController
{

    #[Route("/admin/album", name:"admin_album_index")]
    public function index(EntityManagerInterface $em)
    {
        $albums = $em->getRepository(Album::class)->findAll();

        return $this->render('admin/album/index.html.twig', ['albums' => $albums]);
    }

    #[Route("/admin/album/add", name:"admin_album_add")]
    public function add(Request $request, EntityManagerInterface $em)
    {
        $album = new Album();
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($album);
            $em->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/add.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/admin/album/update/{id}", name:"admin_album_update")]
    public function update(Request $request, int $id, EntityManagerInterface $em)
    {
        $album = $em->getRepository(Album::class)->find($id);
        $form = $this->createForm(AlbumType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('admin_album_index');
        }

        return $this->render('admin/album/update.html.twig', ['form' => $form->createView()]);
    }

    #[Route("/admin/album/delete/{id}", name:"admin_album_delete")]
    public function delete(int $id, EntityManagerInterface $em)
    {
        $media = $em->getRepository(Album::class)->find($id);
        $em->remove($media);
        $em->flush();

        return $this->redirectToRoute('admin_album_index');
    }
}