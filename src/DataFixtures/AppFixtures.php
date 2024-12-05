<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Album;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher, private EntityManagerInterface $em)
    {
        $this->passwordHasher = $passwordHasher;
        $this->em = $em;
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
        $manager->flush();
        $ina = $this->em->getRepository(User::class)->findOneBy(['name' => 'Ina']);
        $startNumberOfGuests = $ina? $ina->getId()+1 : 1;
        $numberOfGuests = 100;
        // Création de 100 guests
        $j=0;
        for ($i = $startNumberOfGuests; $i <= $startNumberOfGuests + $numberOfGuests; $i++) {
            $j += 1;
            $userGuest = new User();
            $userGuest->setName('Guest' . $j);
            $userGuest->setEmail('guest' . $j . '@gmail.com');
            $hashedPassword = $this->passwordHasher->hashPassword($userGuest, 'test');
            $userGuest->setPassword($hashedPassword);
            $userGuest->setAdmin(false);
            $userGuest->setDescription('Guest' . $j);
            $userGuest->setRoles(['ROLE_USER']);
            $userGuest->setAccess(true);
            $manager->persist($userGuest);
        }
        $manager->flush();
        //Création de 5 albums
        for ($i = 1; $i <= 5; $i++) {
            $album = new Album();
            $album->setName('Album' . $i);
            $manager->persist($album);
        }
        $manager->flush();
        
        $albums = $this->em->getRepository(Album::class)->findOneBy(['name' => 'Album1']);
        //Création de 5051 medias
        $guests = $this->em->getRepository(User::class)->findAll();
        $albums = $this->em->getRepository(Album::class)->findAll();
        for ($i = 1; $i <= 5000; $i++) {
            $media = new Media();
            $media->setTitle('Titre ' . $i);
            $countNumberOfI = strlen((string)$i);
            $zeroString = '';
            for ($j = $countNumberOfI; $j < 4; $j++) {
                $zeroString .= '0';
            } 
            $media->setPath('uploads/' . $zeroString . $i . '.jpg');
            $maxGuests = count($guests);
            if ($maxGuests > 0) {
                $media->setUser($guests[random_int(0, $maxGuests-1)]);
            }
            $manager->persist($media);
        }
        for ($i = 5001; $i <= 5050; $i++) {
            $media = new Media();
            $media->setTitle('Titre ' . $i);
            $countNumberOfI = strlen((string)$i);
            $zeroString = '';
            for ($j = $countNumberOfI; $j < 4; $j++) {
                $zeroString .= '0';
            } 
            $media->setPath('uploads/' . $zeroString . $i . '.jpg');
            if(count($guests) > 0) {
                $media->setUser($guests[random_int(0, $numberOfGuests-1)]);
            }
            $countAlbums = count($albums);
            if ($countAlbums > 0) {
                $media->setAlbum($albums[random_int(0, $countAlbums-1)]);
            }
            $manager->persist($media);
        }
        $manager->flush();
    }
}
