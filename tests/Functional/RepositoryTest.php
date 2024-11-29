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
    public function testUserRepo()
    {
        //test UpgradePassword that work
        $user = $this->getEntityManager()->getRepository(User::class)->findOneById(1);
        $HashedPassword = $this->service(UserPasswordHasherInterface::class)->hashPassword($user, ConstForTest::NEW_PASSWORD);
        $this->getEntityManager()->getRepository(User::class)->UpgradePassword($user, $HashedPassword);
        self::assertEquals($HashedPassword, $user->getPassword());
        // manip inverse
        $HashedPassword = $this->service(UserPasswordHasherInterface::class)->hashPassword($user, ConstForTest::PASSWORD);
        $this->getEntityManager()->getRepository(User::class)->UpgradePassword($user, $HashedPassword);
        self::assertEquals($HashedPassword, $user->getPassword());
    }
    public function testMediaRepo()
    {
        //test fingbyusernotlocked on prends Ina dans ce cas car elle n'est pas bloquée
        $user = $this->getEntityManager()->getRepository(User::class)->findOneByName(ConstForTest::USERNAME);
        $user->setAccess(false);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
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