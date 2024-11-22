<?php 

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;

class loginTest extends WebTestCase
{
    protected KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testLoginThatSucceed()
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Connexion');

        $form = $crawler->selectButton('Connexion')->form();
        $form['_username'] = 'Ina';
        $form['_password'] = 'test';
        $this->client->submit($form);
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('IS_AUTHENTICATED'));

        $this->client->request('GET', '/logout');
        self::assertFalse($authorizationChecker->isGranted('IS_AUTHENTICATED'));
    }

    /**
     * @template T
     * @param class-string<T> $id
     * @template T
     */
    protected function service(string $id): object
    {
        return $this->client->getContainer()->get($id);
    }

}