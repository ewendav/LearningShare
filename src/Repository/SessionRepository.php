<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    //    /**
    //     * @return Session[] Returns an array of Session objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Session
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Returns all sessions having an Exchange attached.
     *
     * @return Session[]
     */
    public function findAllExchanges(): array
    {
        return $this->createQueryBuilder('s')
        ->join('s.exchange', 'e')
        ->getQuery()
        ->getResult();
    }

    /**
     * Returns all sessions having a Lesson attached.
     *
     * @return Session[]
     */
    public function findAllLessons(): array
    {
        return $this->createQueryBuilder('s')
        ->join('s.lesson', 'l')
        ->getQuery()
        ->getResult();
    }

    public function searchLessons(
        ?User $user,
        string $q,
        ?Category $categoryGiven,
        ?string $skillGiven,
        ?\DateTimeInterface $dateStart,
        ?\DateTimeInterface $dateEnd,
        string $timeStart,
        string $timeEnd
    ): array {
        $qb = $this->createQueryBuilder('s')
        ->join('s.lesson', 'l')
        ->join('s.skillTaught', 'st')
        ->addSelect('l', 'st')
        ->leftJoin('l.attendees', 'a');

        if ($user) {
            $qb->andWhere('l.host != :user')
               ->andWhere(':user NOT MEMBER OF l.attendees')
               ->setParameter('user', $user);
        }

        if ('' !== $q) {
            $qb->andWhere('st.name LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if (null !== $categoryGiven) {
            $qb->andWhere('st.category = :category')
               ->setParameter('category', $categoryGiven);
        }

        if ($dateStart) {
            $qb->andWhere('s.date = :dateStart')
               ->setParameter('dateStart', $dateStart->format('Y-m-d'));
        }

        // if ($dateEnd) {
        //     $qb->andWhere('s.date = :dateEnd')
        //        ->setParameter('dateEnd', $dateEnd->format('Y-m-d'));
        // }

        if ('' !== $timeStart) {
            $qb->andWhere('s.startTime >= :timeStart')
               ->setParameter('timeStart', $timeStart);
        }

        if ('' !== $timeEnd) {
            $qb->andWhere('s.endTime <= :timeEnd')
               ->setParameter('timeEnd', $timeEnd);
        }

        $qb->groupBy('s.id')
           ->having('COUNT(a.id) < l.maxAttendees');

        return $qb->getQuery()->getResult();
    }

    public function searchExchanges(
        ?User $user,
        string $q,
        ?Category $categoryGiven,
        ?Category $categoryRequested,
        string $skillGiven,
        string $skillRequested,
        ?\DateTimeInterface $dateStart,
        ?\DateTimeInterface $dateEnd,
        string $timeStart,
        string $timeEnd
    ): array {
        $qb = $this->createQueryBuilder('s')
            ->join('s.exchange', 'e')
            ->join('s.skillTaught', 'st')
            ->join('e.skillRequested', 'sr');

        $qb->andWhere('e.attendee IS NULL');

        if ($user) {
            $qb
               ->andWhere('e.requester != :user')
               ->setParameter('user', $user);
        }

        if ('' !== $q) {
            $qb->andWhere('st.name LIKE :q')
               ->setParameter('q', '%' . $q . '%');
        }

        if (null !== $categoryGiven) {
            $qb->andWhere('st.category = :category')
               ->setParameter('category', $categoryGiven);
        }

        if (null !== $categoryRequested) {
            $qb->andWhere('st.category = :category')
               ->setParameter('category', $categoryGiven);
        }

        if (null !== $skillGiven) {
            $qb->andWhere('sr.name LIKE :skillFilter')
               ->setParameter('skillFilter', '%' . $skillGiven . '%');
        }

        if ($dateStart) {
            $qb->andWhere('s.date = :dateStart')
               ->setParameter('dateStart', $dateStart->format('Y-m-d'));
        }

        // if ($dateEnd) {
        //     $qb->andWhere('s.date = :dateEnd')
        //        ->setParameter('dateEnd', $dateEnd->format('Y-m-d'));
        // }

        if ('' !== $timeStart) {
            $qb->andWhere('s.startTime >= :timeStart')
               ->setParameter('timeStart', $timeStart);
        }

        if ('' !== $timeEnd) {
            $qb->andWhere('s.endTime <= :timeEnd')
               ->setParameter('timeEnd', $timeEnd);
        }

        return $qb->getQuery()->getResult();
    }
}
