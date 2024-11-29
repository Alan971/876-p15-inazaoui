<?php 

declare(strict_types=1);

namespace App\tests\Functional;

use App\Tests\Functional\FunctionalTestCase;
use App\Entity\User;


class ControllerTest extends FunctionalTestCase
{
    public function testHome()
    {
        $this->client->request('GET', '/', []);
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h2', 'Photographe');
    }

    public function testAbout()
    {
        $this->client->request('GET', '/about', []);
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h2', 'Qui suis-je ?');
    }

    public function testPortfolio()
    {
        //test portfolio général
        foreach ($this->getEntityManager()->getRepository(User::class)->findAll() as $user) {
            $id = $user->getId();
            $this->client->request('GET', '/portfolio/' . $id , []);
            $this->assertResponseIsSuccessful();
            $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
            $this->assertSelectorTextContains('h3', 'Portfolio');
        }
    }

    public function testGuests()
    {
        //test guests général
        $this->client->request('GET', '/guests', []);
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h3', 'Invités');
    }
    public function testGuest()
    {
        //test guest général
        $guest = $this->getEntityManager()->getRepository(User::class)->findAll()[0];
        $id = $guest->getId();
        $this->client->request('GET', '/guest/' . $id , []);
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h3', $guest->getName());
    }
    public function testGuestNotFound()
    {
        //test guest non trouvé
        $guest = $this->getEntityManager()->getRepository(User::class)->findAll()[0];
        $id = 10000000;
        $this->client->request('GET', '/guest/' . $id , []);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects('/guests');
    }
    /**
     * @depends testGuestNotFound
     */
    public function testGuestLocked()
    {
        // choix d'un invité au hasard, controle de son état d'accès et changement de l'état si nécessaire
        $guests = $this->getEntityManager()->getRepository(User::class)->findAll();
        shuffle($guests);
        $guest = $guests[0];
        $id = $guest->getId();
        if ($status = $guest->getAccess()) {
            $guest->setAccess(false);
            $this->getEntityManager()->persist($guest);
            $this->getEntityManager()->flush();
        }
        // test guest bloqué
        $this->client->request('GET', '/guest/' . $id , []);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects('/guests');
        // modification de l'état d'accès si nécessaire
        if ($status) {
            $guest->setAccess(true);
            $this->getEntityManager()->persist($guest);
            $this->getEntityManager()->flush();
        }
    }

}