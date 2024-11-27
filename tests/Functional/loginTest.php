<?php 

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use App\Tests\Functional\FunctionalTestCase;

class loginTest extends FunctionalTestCase
{

    public function testLoginThatSucceed()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Connexion');

        $this->login('Ina', 'test');
        
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED'));

        $this->client->request('GET', '/logout');
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

}