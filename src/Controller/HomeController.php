<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class HomeController extends AbstractController
{
    #[Route("/", name:"home")]
    public function home() : Response
    {
        return $this->render('front/home.html.twig');
    }

    #[Route("/guests", name:"guests")]
    public function guests(EntityManagerInterface $em) : Response
    {
        $guests = $em->getRepository(User::class)->findBy(['admin' => false, 'access' => true]);
        return $this->render('front/guests.html.twig', [
            'guests' => $guests
        ]);
    }

    #[Route("/guest/{id}", name:"guest")]
    public function guest(int $id, EntityManagerInterface $em) : Response
    {
        $guest = $em->getRepository(User::class)->find($id);
        if (!$guest) {
            $this->addFlash('danger', 'utilisateur non trouvé');
            return $this->redirectToRoute('guests');
        }
        if ($guest->getAccess() === false) {
            $this->addFlash('danger', 'Le travail de cet invité n\'est plus disponible');
            return $this->redirectToRoute('guests');
        }
        return $this->render('front/guest.html.twig', [
            'guest' => $guest
        ]);
    }

    #[Route("/portfolio/{id}", name:"portfolio")]
    public function portfolio(EntityManagerInterface $em, ?int $id = null) : Response
    {
        $albums = $em->getRepository(Album::class)->findAll();
        $album = $id ? $em->getRepository(Album::class)->find($id) : null;
        //remplacement de la définition de l'utilisateur par l'utilisateur connecté
        // $user = $em->getRepository(User::class)->findOneByAdmin(true);
        $user = $this->getUser();
        $usersNotLocked = $em->getRepository(User::class)->findByAccess(true);

        $medias = $album
            ? $em->getRepository(Media::class)->findByAlbumUserNotLocked($album, $usersNotLocked)
            : $em->getRepository(Media::class)->findByUserNotLocked($user);
        return $this->render('front/portfolio.html.twig', [
            'albums' => $albums,
            'album' => $album,
            'medias' => $medias
        ]);
    }

    #[Route("/about", name:"about")]
    public function about(): Response
    {
        return $this->render('front/about.html.twig');
    }
}