<?php 

declare(strict_types=1);

namespace App\tests\Functional;

use App\Tests\Functional\FunctionalTestCase;
use App\Entity\User;


class AdminControllerTest extends FunctionalTestCase
{
    public function testAdminNotConnected()
    {
        $this->client->request('GET', '/admin/album', []);
        $this->assertResponseIsSuccessful();
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminAlbum()
    {
        //connexion admin
        $this->login('Ina', 'test');
        $this->client->request('GET', '/admin/album', []);
        $this->assertResponseIsSuccessful();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Albums');
    }

}