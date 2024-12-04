<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Functional\FunctionalTestCase;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Album;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\DataFixtures\AppFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;


class AppFixturesTest extends FunctionalTestCase
{
    public function testFixtures(): void
    {
        $objectManager = $this->createMock(ObjectManager::class);

        $objectManager->expects($this->exactly(102))
            ->method('persist')
            ->with($this->isInstanceOf(User::class));
        $objectManager->expects($this->exactly(5))
            ->method('persist')
            ->with($this->isInstanceOf(Album::class));
        $objectManager->expects($this->exactly(5050))
            ->method('persist')
            ->with($this->isInstanceOf(Media::class));

        $objectManager->expects($this->exactly(4))
            ->method('flush');

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->exactly(4))
            ->method('getRepository')
            ->willReturn($this->createMock(EntityRepository::class));

        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $passwordHasher->expects($this->exactly(102))
            ->method('hashPassword')
            ->willReturn('hashedPassword');

        $usersFixtures = new AppFixtures($passwordHasher, $em);
        $usersFixtures->load($objectManager);

    }
}