<?php 

declare(strict_types=1);

namespace App\tests\Functional;

use App\Tests\Functional\FunctionalTestCase;
use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use App\Tests\ConstForTest;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class AdminControllerTest extends FunctionalTestCase
{
    public function testAdminNotConnected(): void
    {
        $this->client->request('GET', '/admin/album', []);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $location = $this->client->getResponse()->headers->get('location');
        if($location === null){
            throw new \Exception('location null');
        }
        $this->assertStringContainsString('http://localhost/login', $location);
    }

    public function testAdminAlbum(): void
    {
        //connexion admin et arrivée sur la page admin/album
        $this->login(ConstForTest::USERNAME, ConstForTest::PASSWORD);
        $this->client->request('GET', '/admin/album', []);
        $this->assertResponseIsSuccessful();
        self::assertSelectorTextNotContains('h1', 'Albums');
        /** @var AuthorizationCheckerInterface $authorizationChecker */
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('ROLE_ADMIN'));

        // test de ADD  
        $crawler = $this->client->request('GET', '/admin/album/add', []);
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form();
        $form['album[name]'] = ConstForTest::ALBUM_NAME;
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $album = $this->getEntityManager()->getRepository(Album::class)->findOneBy(['name' => ConstForTest::ALBUM_NAME]);
        if ($album === null) {
            throw new \Exception('Album not found');
        }
        self::assertEquals('test', $album->getName());
        // test de DELETE NON CONCLUANT quand il contient des medias
        $media = new Media;
        $media->setTitle(ConstForTest::MEDIA_TITLE);
        $media->setAlbum($album);
        $media->setPath(ConstForTest::IMGPATH);
        $this->getEntityManager()->persist($media);
        $this->getEntityManager()->flush();
        $this->client->request('GET', '/admin/album/delete/' . $album->getId());
        $album = $this->getEntityManager()->getRepository(Album::class)->findOneBy(['name' => ConstForTest::ALBUM_NAME]);
        if ($album === null) {
            throw new \Exception('Album not found : Test is Not correct');
        }
        self::assertEquals('test', $album->getName());
        //suppression du media créé
        /** @var Media $media */
        $media = $this->getEntityManager()->getRepository(Media::class)->find($media->getId());
        $this->getEntityManager()->remove($media);
        $this->getEntityManager()->flush();

        // test UPDATE
        $crawler = $this->client->request('GET', '/admin/album/update/' . $album->getId());
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Modifier')->form();
        $form['album[name]'] = ConstForTest::ALBUM_NAME . 'Modif';
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        /** @var Album $album */
        $album = $this->getEntityManager()->getRepository(Album::class)->findOneBy(['name' => ConstForTest::ALBUM_NAME . 'Modif']); 

        // test de DELETE réussi
        $this->client->request('GET', '/admin/album/delete/' . $album->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $album = $this->getEntityManager()->getRepository(Album::class)->findOneBy(['name' => ConstForTest::ALBUM_NAME . 'Modif']);
        self::assertNull($album);
    }

    public function testAdminMedia(): void
    {
        //test d'accès à index des média en admin
        $this->login(ConstForTest::USERNAME, ConstForTest::PASSWORD);
        $crawler = $this->client->request('GET', '/admin/media');
        $this->assertResponseIsSuccessful();
        /** @var AuthorizationCheckerInterface $authorizationChecker */
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('ROLE_ADMIN'));

        // Test index des médias en role user
        $this->login(ConstForTest::USERNAME_GUEST, ConstForTest::PASSWORD);
        $crawler = $this->client->request('GET', '/admin/media');
        $this->assertResponseIsSuccessful();
        /** @var AuthorizationCheckerInterface $authorizationChecker */
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('ROLE_USER'));

        // Test ADD  média en admin
        $this->login(ConstForTest::USERNAME, ConstForTest::PASSWORD);
        $crawler = $this->client->request('GET', '/admin/media/add');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Ajouter')->form();
        $form['media[title]'] = ConstForTest::MEDIA_TITLE;
        $form['media[user]'] = "1";
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = ConstForTest::getUploadedFile(true);
        $form['media[file]'] = $uploadedFile;
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $media = $this->getEntityManager()->getRepository(Media::class)->findOneBy(['title' => ConstForTest::MEDIA_TITLE]);
        if ($media === null) {
            throw new \Exception('Media not found');
        }
        self::assertEquals(ConstForTest::MEDIA_TITLE, $media->getTitle());

        // Test DELETE média
        $this->client->request('GET', '/admin/media/delete/' . $media->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $media = $this->getEntityManager()->getRepository(Media::class)->find($media->getId());
        self::assertNull($media);

        // Test ADD  média en role user
        $this->login(ConstForTest::USERNAME_GUEST, ConstForTest::PASSWORD);
        $crawler = $this->client->request('GET', '/admin/media/add');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form();
        $form['media[title]'] = ConstForTest::MEDIA_TITLE;
        
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = ConstForTest::getUploadedFile(true);
        $form['media[file]'] = $uploadedFile;
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $media = $this->getEntityManager()->getRepository(Media::class)->findOneBy(['title' => ConstForTest::MEDIA_TITLE]);
        if ($media === null) {
            throw new \Exception('Media not found');
        }
        self::assertEquals(ConstForTest::MEDIA_TITLE, $media->getTitle());
        // Test DELETE média
        $this->client->request('GET', '/admin/media/delete/' . $media->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $media = $this->getEntityManager()->getRepository(Media::class)->find($media->getId());
        self::assertNull($media);
    }

    public function testAdminMediaAddNoFileBadFile(): void
    {
        $this->login(ConstForTest::USERNAME, ConstForTest::PASSWORD);

        // Test ADD  média sans fichier
        $crawler = $this->client->request('GET', '/admin/media/add');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form();
        $form['media[title]'] = ConstForTest::MEDIA_TITLE;
        $form['media[user]'] = '1';
        $this->client->submit($form);
        $this->assertEquals(500, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminGuestnotConnected(): void
    {
        $this->client->request('GET', '/admin/guests', []);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $location = $this->client->getResponse()->headers->get('location');
        if($location === null){
            throw new \Exception('location null');
        }
        $this->assertStringContainsString('http://localhost/login', $location);
    }

    public function testAdminGuests(): void
    {
        //connexion admin et arrivée sur la page admin/guests
        $this->login(ConstForTest::USERNAME, ConstForTest::PASSWORD);
        $this->client->request('GET', '/admin/guests', []);
        $this->assertResponseIsSuccessful();
        /** @var  AuthorizationCheckerInterface $authorizationChecker */
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('ROLE_ADMIN'));

        // Test ADD  invité
        $crawler = $this->client->request('GET', '/admin/guests/add');
        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[name]'] = ConstForTest::NEW_USERNAME;
        $form['user[email]'] = ConstForTest::USER_MAIL_ADRESS;
        $form['user[description]'] = ConstForTest::DESCRIPTION;
        $form['user[access]'] = '1';
        $form['user[password][first]'] = ConstForTest::PASSWORD;
        $form['user[password][second]'] = ConstForTest::PASSWORD;
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $guest = $this->getEntityManager()->getRepository(User::class)->findOneBy(['name' => ConstForTest::NEW_USERNAME]);
        if ($guest === null) {
            throw new \Exception('Guest not found');
        }
        self::assertEquals(ConstForTest::NEW_USERNAME, $guest->getName());
        self::assertEquals(ConstForTest::USER_MAIL_ADRESS, $guest->getEmail());

        // test du user bloqué doubler pour avoir les deux états
        $this->client->request('GET', '/admin/guests/lock/' . $guest->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        /** @var User $guest */
        $guest = $this->getEntityManager()->getRepository(User::class)->find($guest->getId());
        self::assertEquals(false, $guest->getAccess());

        // tentative de connexion avec l'utilisateur bloqué
        $this->login(ConstForTest::NEW_USERNAME, ConstForTest::PASSWORD);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $location = $this->client->getResponse()->headers->get('location');
        if($location === null){
            throw new \Exception('location null');
        }
        $this->assertStringContainsString('login', $location);
        //connexion admin pour débloquer l'utilisateur
        $this->login(ConstForTest::USERNAME, ConstForTest::PASSWORD);
        $this->client->request('GET', '/admin/guests/lock/' . $guest->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        /** @var User $guest */
        $guest = $this->getEntityManager()->getRepository(User::class)->find($guest->getId());
        self::assertEquals(true, $guest->getAccess());

        // Test DELETE invité
        $this->client->request('GET', '/admin/guests/delete/' . $guest->getId());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $guest = $this->getEntityManager()->getRepository(User::class)->find($guest->getId());
        self::assertNull($guest);
    }

}