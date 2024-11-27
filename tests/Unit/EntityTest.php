<?php 

declare(strict_types=1);

namespace App\tests\Unit;

use App\Tests\Functional\FunctionalTestCase;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Album;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EntityTest extends FunctionalTestCase
{

    public function testUser()
    {

        $user = new User();

        $user->setName('abc');
        $user->setEmail('abc@gmail.com');
        $hashedPassword = $this->service(UserPasswordHasherInterface::class)->hashPassword($user, '123456');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        $user->setAdmin(false);
        $user->setAccess(true);
        $user->setDescription('abc');

        self::assertEquals('abc', $user->getName());
        self::assertEquals('abc@gmail.com', $user->getEmail());
        self::assertEquals($hashedPassword, $user->getPassword());
        self::assertEquals(['ROLE_USER'], $user->getRoles());
        self::assertEquals(false, $user->isAdmin());
        self::assertEquals(true, $user->getAccess());
        self::assertEquals('abc', $user->getDescription());


        $user = $this->getEntityManager()->getRepository(User::class)->findOneById(1);
        self::assertEquals(1, $user->getId());
        self::assertNotNull($user->getMedias());

    }

    public function testMedia()
    {
        $media = new Media();
        $album = new Album();
        $user = new User();
        $media->setTitle('abc');
        $media->setAlbum($album);
        $media->setPath('abcde');
        $media->setUser($user);

        self::assertEquals('abc', $media->getTitle());
        self::assertEquals($album, $media->getAlbum());
        self::assertEquals('abcde', $media->getPath());
        self::assertEquals($user, $media->getUser());

        $medias = $this->getEntityManager()->getRepository(Media::class)->findAll();
        $media = $medias[0];
        self::assertNotNull($media->getId());

        $file = new UploadedFile(
            dirname(__FILE__) . '/imgTest/test.jpg',
            'test.jpg',
            'image/jpg',
            null,
            true
        );
        $media->setFile($file);
        self::assertNotEmpty($media->getFile());
    }
    
    public function testAlbum()
    {
        $album = new Album();
        $user = new User();
        $album->setName('abc');

        self::assertEquals('abc', $album->getName());

        $albums = $this->getEntityManager()->getRepository(Album::class)->findAll();
        $album = $albums[0];
        self::assertNotNull($album->getId());
    }

}