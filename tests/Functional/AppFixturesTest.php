<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Functional\FunctionalTestCase;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Tests\ConstForTest;
use App\DataFixtures\AppFixtures;
use Doctrine\Persistence\ObjectManager;

class AppFixturesTest extends FunctionalTestCase
{
    public function testFixtures()
    {
        $objectManager = $this->createMock(ObjectManager::class);
        $objectManager->expects($this->exactly(3))
            ->method('persist')
            ->with($this->isInstanceOf(User::class));

        $objectManager->expects($this->once())
            ->method('flush');

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasher->expects($this->exactly(3))
            ->method('hashPassword')
            ->willReturn('hashedPassword');

        $usersFixtures = new AppFixtures($passwordHasher);
        $usersFixtures->load($objectManager);

    }
}