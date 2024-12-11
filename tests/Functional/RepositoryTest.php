<?php

declare(strict_types=1);

namespace App\tests\Functional;

use App\Tests\Functional\FunctionalTestCase;
use App\Entity\User;
use App\Entity\Media;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Tests\ConstForTest;

class RepositoyTest extends FunctionalTestCase
{
    public function testUserRepo(): void
    {
        //test UpgradePassword that work
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->findOneById(ConstForTest::getInaId($this->getEntityManager()));
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $this->service(UserPasswordHasherInterface::class);
        $hashedPassword = $hasher->hashPassword($user, ConstForTest::NEW_PASSWORD);
        $this->getEntityManager()->getRepository(User::class)->UpgradePassword($user, $hashedPassword);
        self::assertEquals($hashedPassword, $user->getPassword());
        // manip inverse
        $hashedPassword = $hasher->hashPassword($user, ConstForTest::PASSWORD);
        $this->getEntityManager()->getRepository(User::class)->UpgradePassword($user, $hashedPassword);
        self::assertEquals($hashedPassword, $user->getPassword());
    }
    public function testMediaRepo(): void
    {
        //test fingbyusernotlocked on prends Ina dans ce cas car elle n'est pas bloquée
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->findOneByName(ConstForTest::USERNAME);
        $user->setAccess(false);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        /** @var array<int, Media> $medias */
        $medias = $this->getEntityManager()->getRepository(Media::class)->findByUserNotLocked($user);
        self::assertEquals(0, count($medias));
        // manip inverse
        $user->setAccess(true);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        if ($user->getAccess()) {
            $medias = $this->getEntityManager()->getRepository(Media::class)->findByUserNotLocked($user);
            self::assertNotNull($medias);
        }
        self::assertNotNull($user->getAccess());
    }
}