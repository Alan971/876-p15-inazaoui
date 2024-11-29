<?php 

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Tests\Functional\FunctionalTestCase;
use App\tests\ConstForTest;

class loginTest extends FunctionalTestCase
{

    public function testLoginThatSucceed()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Connexion');

        $this->login(ConstForTest::USERNAME, ConstForTest::PASSWORD);

        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED'));

        $this->client->request('GET', '/logout');
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

    public function testLoginThatFail()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Connexion');

        $this->login('toto', 'toto');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

}