<?php 

declare(strict_types=1);

namespace App\Tests;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ConstForTest
{
    const IMGPATH = __DIR__ . '/imgTest/test1.png';
    const IMGNAME = 'test1.png';
    const MEDIA_TITLE = 'Titre 1';
    const DESCRIPTION = 'ceci est un test de description';
    const ALBUM_NAME = 'Album1';
    const USERNAME = 'Ina';
    const PASSWORD = 'test';
    const USERNAME_GUEST = 'Guest1';
    const NEW_PASSWORD = 'testNew';
    const NEW_USERNAME = 'NewName';
    const USER_MAIL_ADRESS = 'abc@gmail.com';


    public static function getUploadedFile(Bool $isFileExist ): UploadedFile
    {
        $imgPath = "fichierInvalide";
        if ($isFileExist) {
            $imgPath = self::IMGPATH;
        }
        return new UploadedFile(
            $imgPath,  // Chemin vers le fichier temporaire
            self::IMGNAME,        // Nom du fichier
            'image/png',    // Type MIME
            null,           // Taille du fichier (laisser null pour tester sans taille)
            true            // Si le fichier existe réellement (mettre true ou false selon le test)
        );
        
    }
    public static function getInaId(EntityManagerInterface $entityManager): int
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['name' => self::USERNAME]);
        if ($user === null) {
            throw new \Exception('User not found');
        }
        return $user->getId();
    }

}