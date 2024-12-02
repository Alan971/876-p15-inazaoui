<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Album;
use Doctrine\Common\Collections\Collection;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array<string, mixed> $criteria, array<string|null> $orderBy = null)
 * @method Media[]    findByUser(UserInterface $user)
 * @method Media[]    findByAlbum(int $albumId)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array<string, mixed> $criteria, array<string|null> $orderBy = null, $limit = null, $offset = null)
 */
class MediaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Media::class);
    }


    /**
     * Récupère tous les médias d'un utilisateur qu n'est pas bloqué
     *
     * @param ?UserInterface $user
     * @return array<int, Media>    Returns an array of Media objects
     */ 
    public function findByUserNotLocked(?UserInterface $user): array
    {
        /** @var \App\Entity\User $user */
        if ($user instanceof UserInterface) {
            if ($user->getAccess() === true) {
                return self::findByUser($user);
            }
        }
        return [];
    }

    /**
    * Récupère tous les médias d'un album dont les auteurs ne sont pas bloqués
    *
    * @param Album $album
    * @param array<int, \App\Entity\User> $users
    * @phpstan-return array<mixed, mixed>      
    */
    public function findByAlbumUserNotLocked(Album $album,array $users): array
    {
        $result = $this->createQueryBuilder('m')
            ->andWhere('m.album = :album')
            ->andWhere('m.user IN (:users)')
            ->setParameter('album', $album)
            ->setParameter('users', $users)
            ->getQuery()
            ->getResult()
        ;
        return (array) $result;
    }
//    /**
//     * @return Media[] Returns an array of Media objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Media
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
