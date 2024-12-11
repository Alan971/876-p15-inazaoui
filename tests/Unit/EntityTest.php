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
use App\Tests\ConstForTest;

class EntityTest extends FunctionalTestCase
{

    public function testUser(): void
    {

        $user = new User();

        $user->setName(ConstForTest::NEW_USERNAME);
        $user->setEmail(ConstForTest::USER_MAIL_ADRESS);
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $this->service(UserPasswordHasherInterface::class);
        $hashedPassword = $passwordHasher->hashPassword($user, ConstForTest::PASSWORD);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        $user->setAdmin(false);
        $user->setAccess(true);
        $user->setDescription(ConstForTest::DESCRIPTION);

        self::assertEquals(ConstForTest::NEW_USERNAME, $user->getName());
        self::assertEquals(ConstForTest::USER_MAIL_ADRESS, $user->getEmail());
        self::assertEquals($hashedPassword, $user->getPassword());
        self::assertEquals(['ROLE_USER'], $user->getRoles());
        self::assertEquals(false, $user->isAdmin());
        self::assertEquals(true, $user->getAccess());
        self::assertEquals(ConstForTest::DESCRIPTION, $user->getDescription());

        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->findOneById(ConstForTest::getInaId($this->getEntityManager()));
        self::assertEquals(ConstForTest::getInaId($this->getEntityManager()), $user->getId());
        self::assertNotNull($user->getMedias());

    }

    public function testMedia(): void
    {
        $media = new Media();
        $album = new Album();
        $user = new User();
        $media->setTitle(ConstForTest::MEDIA_TITLE);
        $media->setAlbum($album);
        $media->setPath(ConstForTest::IMGPATH);
        $media->setUser($user);

        self::assertEquals(ConstForTest::MEDIA_TITLE, $media->getTitle());
        self::assertEquals($album, $media->getAlbum());
        self::assertEquals(ConstForTest::IMGPATH, $media->getPath());
        self::assertEquals($user, $media->getUser());

        $medias = $this->getEntityManager()->getRepository(Media::class)->findAll();
        $media = $medias[0];
        self::assertNotNull($media->getId());

        $file = ConstForTest::getUploadedFile(true);
        $media->setFile($file);
        self::assertNotEmpty($media->getFile());
    }
    
    public function testAlbum(): void
    {
        $album = new Album();
        $user = new User();
        $album->setName(ConstForTest::ALBUM_NAME);

        self::assertEquals(ConstForTest::ALBUM_NAME, $album->getName());

        $albums = $this->getEntityManager()->getRepository(Album::class)->findAll();
        $album = $albums[0];
        self::assertNotNull($album->getId());
    }

}