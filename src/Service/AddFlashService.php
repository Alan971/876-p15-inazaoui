<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class AddFlashService
{
    public function __construct(private RequestStack  $requestStack)
    {
    }

    public function addFlash(string $type, string $message): void
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if ($session instanceof Session) {
            $session->getFlashBag()->add($type, $message);
        }
    }
}