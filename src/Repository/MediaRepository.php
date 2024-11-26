<?php

namespace App\Repository;

use App\Entity\Media;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Album;

/**
 * @extends ServiceEntityRepository<Media>
 *
 * @method Media|null find($id, $lockMode = null, $lockVersion = null)
 * @method Media|null findOneBy(array $criteria, array $orderBy = null)
 * @method Media[]    findAll()
 * @method Media[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
     * @param UserInterface $user
     * @return array    Returns an array of Media objects
     */ 
    public function findByUserNotLocked(UserInterface $user): array
    {
        if ($user->getAccess() === true) {
            return self::findByUser($user);
        }
        return [];
    }

    /**
     * Récupère tous les médias d'un album dont les auteurs ne sont pas bloqués
     *
     * @param Album $album
     * @param array $users
     * @return array
     */
    public function findByAlbumUserNotLocked(Album $album,array $users): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.album = :album')
            ->andWhere('m.user IN (:users)')
            ->setParameter('album', $album)
            ->setParameter('users', $users)
            ->getQuery()
            ->getResult()
        ;
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
