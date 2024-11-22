<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // création du premier utilisateur admin
        $userIna = new User();
        $userIna->setName('Ina');
        $userIna->setEmail('ina@gmail.com');
        $hashedPassword = $this->passwordHasher->hashPassword($userIna, 'test');
        $userIna->setPassword($hashedPassword);
        $userIna->setAdmin(true);
        $userIna->setDescription('Ina');
        $userIna->setRoles(['ROLE_ADMIN']);
        $manager->persist($userIna);

        $manager->flush();
    }
}
