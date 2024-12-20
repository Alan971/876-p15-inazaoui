<?php 

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Tests\Functional\FunctionalTestCase;
use App\Tests\ConstForTest;

class loginTest extends FunctionalTestCase
{

    public function testLoginThatSucceed(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Connexion');

        $this->login(ConstForTest::USERNAME, ConstForTest::PASSWORD);

        /** @var  AuthorizationCheckerInterface $authorizationChecker */
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED'));

        $this->client->request('GET', '/logout');
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

    public function testLoginThatFail(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Connexion');

        $this->login('toto', 'toto');
        self::assertEquals(302, $this->client->getResponse()->getStatusCode());
        
        /** @var  AuthorizationCheckerInterface $authorizationChecker */
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

}