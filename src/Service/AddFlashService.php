<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

class AddFlashService
{
    public function __construct(private RequestStack  $requestStack)
    {
    }

    public function addFlash(string $type, string $message): void
    {
        $requestStack = $this->requestStack->getCurrentRequest();
        if ($requestStack instanceof Request) {
            $session = $requestStack->getSession();
            if ($session instanceof Session) {
                $session->getFlashBag()->add($type, $message);
            }
        }
    }
}