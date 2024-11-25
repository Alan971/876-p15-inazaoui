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
        $userIna->setAccess(true);
        $manager->persist($userIna);

        // Création de 2 guests
        $userGuest1 = new User();
        $userGuest1->setName('Guest1');
        $userGuest1->setEmail('guest1@gmail.com');
        $hashedPassword = $this->passwordHasher->hashPassword($userGuest1, 'test');
        $userGuest1->setPassword($hashedPassword);
        $userGuest1->setAdmin(false);
        $userGuest1->setDescription('Guest1');
        $userGuest1->setRoles(['ROLE_USER']);
        $userGuest1->setAccess(true);
        $manager->persist($userGuest1);

        $userGuest2 = new User();
        $userGuest2->setName('Guest2');
        $userGuest2->setEmail('guest2@gmail.com');
        $hashedPassword = $this->passwordHasher->hashPassword($userGuest2, 'test');
        $userGuest2->setPassword($hashedPassword);
        $userGuest2->setAdmin(false);
        $userGuest2->setDescription('Guest2');
        $userGuest2->setRoles(['ROLE_USER']);
        $userGuest2->setAccess(false);   
        $manager->persist($userGuest2);

        $manager->flush();
    }
}
