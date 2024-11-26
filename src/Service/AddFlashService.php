<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class AddFlashService
{
    public function __construct(private RequestStack  $session)
    {
        $this->session = $session;
    }

    public function addFlash(string $type, string $message): void
    {
        $this->session->getSession()->getFlashBag()->add($type, $message);
    }
}