<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

final class LockedAccountListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[AsEventListener(event: LoginSuccessEvent::class)]
    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        // mon code tout simple
        if ($user instanceof UserInterface && $user->getAccess() === false) {
            

            // Déconnecter l'utilisateur et rediriger
            $this->security->getTokenStorage()->setToken(null); // Déconnecte l'utilisateur
            $event->getRequest()->getSession()->invalidate(); // Invalide la session
            $event->getRequest()->setSession($event->getRequest()->getSession()); // Rafraîchit la session

            $this->security->getSession()->getFlashBag()->add('danger', 'Votre compte est bloqué, veuillez contacter votre administrateur.');
            // throw new \Exception('Account ' .  $user->getName() . ' is locked, please contact your administrator.');
        }
    }
}
