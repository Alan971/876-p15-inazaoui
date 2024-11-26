<?php

namespace App\EventListener;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Service\AddFlashService;

final class LockedAccountListener
{
    public function __construct(private AddFlashService $flashService, private TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->flashService = $flashService;
    }

    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if ($user instanceof UserInterface && $user->getAccess() === false) {
            // Retirer le jeton de sécurité pour déconnecter l'utilisateur
            $this->tokenStorage->setToken(null);
            // Déconnecter l'utilisateur et rediriger
            $event->getRequest()->getSession()->invalidate(); // Invalide la session
            $event->getRequest()->setSession($event->getRequest()->getSession()); // Rafraîchit la session
            $this->flashService->addFlash('danger', 'Votre compte ' .  $user->getName() . ' est bloqué, veuillez contacter votre administrateur.');
            $event->setResponse(new \Symfony\Component\HttpFoundation\RedirectResponse('/login'));
        }
    }
}
