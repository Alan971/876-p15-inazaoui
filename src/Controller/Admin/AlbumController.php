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
use Symfony\Component\HttpFoundation\Response;

#[IsGranted('ROLE_ADMIN')]
class AlbumController extends AbstractController
{
    #[Route("/admin/album", name:"admin_album_index")]
    public function index(EntityManagerInterface $em) : Response
    {
        $albums = $em->getRepository(Album::class)->findAll();

        return $this->render('admin/album/index.html.twig', ['albums' => $albums]);
    }

    #[Route("/admin/album/add", name:"admin_album_add")]
    public function add(Request $request, EntityManagerInterface $em): Response
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
    public function update(Request $request, int $id, EntityManagerInterface $em): Response
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
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        if(!empty($em->getRepository(Media::class)->findByAlbum($id))) {
            $this->addFlash('danger', 'Cet album est lié à des médias, veuillez les supprimer avant de supprimer cet album');
        }
        else {
            /** @var Album $album */
            $album = $em->getRepository(Album::class)->find($id);
            $em->remove($album);
            $em->flush();
        }

        return $this->redirectToRoute('admin_album_index');
    }
}