<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class GuestsController extends AbstractController
{
    #[Route('/admin/guests', name: 'admin_guests_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $guests = $em->getRepository(User::class)->findBy(['admin' => false]);
        return $this->render('admin/guests/index.html.twig', [
            'guests' => $guests,
        ]);
    }

    #[Route('/admin/guests/lock/{id}', name: 'admin_guest_lock')]
    public function lock(EntityManagerInterface $em, int $id): Response
    {
        $guest = $em->getRepository(User::class)->find($id);
        if ($guest->getAccess()) {
            $guest->setAccess(false);
        } else {
            $guest->setAccess(true);
        }
        $em->persist($guest);
        $em->flush();

        return $this->redirectToRoute('admin_guests_index');
    }

    #[Route('/admin/guests/delete/{id}', name: 'admin_guest_delete')]
    public function delete(EntityManagerInterface $em, int $id): Response
    {
        $guest = $em->getRepository(User::class)->find($id);
        $em->remove($guest);
        $em->flush();

        return $this->redirectToRoute('admin_guests_index');
    }

    #[Route('/admin/guests/add', name: 'admin_guest_add')]
    public function add(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $guest = new User();
        $form = $this->createForm(UserType::class, $guest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $guest->setPassword($passwordHasher->hashPassword($guest, $guest->getPassword()));
            $em->persist($guest);
            $em->flush();
            return $this->redirectToRoute('admin_guests_index');
        }

        return $this->render('admin/guests/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
