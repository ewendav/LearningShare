<?php

namespace App\Repository;

use App\Entity\Session;
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

/**
 * OBSOLETE S
 * Returns all attendable Exchange sessions for the given user.
 *
 * @param User $user
 * @return Session[]
 */
    public function findAttendableExchanges(User $user): array
    {
        return $this->createQueryBuilder('s')
        ->join('s.exchange', 'e')
        ->andWhere('e.attendee IS NULL')
        ->andWhere('e.requester != :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();
    }

/**
 * Returns all attendable Lesson sessions for the given user.
 * OBSOLETE
 *
 * @param User $user
 * @return Session[]
 */
    public function findAttendableLessons(User $user): array
    {
        $qb = $this->createQueryBuilder('s')
        ->join('s.lesson', 'l')
        ->leftJoin('l.attendees', 'a')
        ->andWhere('l.host != :user')
        ->andWhere(':user NOT MEMBER OF l.attendees')
        ->setParameter('user', $user)
        ->groupBy('l.id')
        ->having('COUNT(a) < l.maxAttendees');

        return $qb->getQuery()->getResult();
    }

    public function searchLessons(
        ?User $user,
        string $q,
        string $category,
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

        if ('' !== $category) {
            $qb->andWhere('st.category = :category')
               ->setParameter('category', $category);
        }

        if ($dateStart) {
            $qb->andWhere('s.date >= :dateStart')
               ->setParameter('dateStart', $dateStart->format('Y-m-d'));
        }

        if ($dateEnd) {
            $qb->andWhere('s.date <= :dateEnd')
               ->setParameter('dateEnd', $dateEnd->format('Y-m-d'));
        }

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

    public function searchExchanges(?User $user, string $q, string $category, string $skillFilter, ?\DateTimeInterface $dateStart, ?\DateTimeInterface $dateEnd, string $timeStart, string $timeEnd): array
    {
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

        if ('' !== $category) {
            $qb->andWhere('st.category = :category')
               ->setParameter('category', $category);
        }

        if ('' !== $skillFilter) {
            $qb->andWhere('sr.name LIKE :skillFilter')
               ->setParameter('skillFilter', '%' . $skillFilter . '%');
        }

        if ($dateStart) {
            $qb->andWhere('s.date >= :dateStart')
               ->setParameter('dateStart', $dateStart->format('Y-m-d'));
        }

        if ($dateEnd) {
            $qb->andWhere('s.date <= :dateEnd')
               ->setParameter('dateEnd', $dateEnd->format('Y-m-d'));
        }

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
