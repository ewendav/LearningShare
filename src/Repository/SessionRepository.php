<?php

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

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
            $qb->andWhere('sr.category = :category')
               ->setParameter('category', $categoryRequested);
        }

        if (null !== $skillRequested) {
            $qb->andWhere('sr.name LIKE :skillFilter')
               ->setParameter('skillFilter', '%' . $skillRequested . '%');
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

    /**
        * Retourne, pour $user, toutes les sessions “échanges” et “cours” à venir,
        * en se basant sur les collections déjà chargées dans l’entité User.
        *
        * @param User $user
        * @return array{partage: array<int,mixed>, cours: array<int,mixed>}
        */
    public function getAttendableSessionsByUserEntities(User $user): array
    {
        $now   = new DateTime('now');
        $today = new DateTime('today');

        $partage = [];
        // ————————————————
        // 1) Collecte des “échanges” (Exchange → Session)
        //    • user est “requester” OU “attendee”
        //    • on doit parcourir BOTH exchangesCreated ET exchangesAttended
        //    • ne garder que les sessions dont la date/heure est > maintenant
        // ————————————————

        // 1.1) Échanges créés par $user
        foreach ($user->getExchangesCreated() as $exchange) {
            $session = $exchange->getSession();
            if (! $session) {
                continue;
            }

            // Filtre “à venir”
            $sessionDate = $session->getDate();
            $sessionEnd  = $session->getEndTime();
            if ($sessionDate < $today || ($sessionDate == $today && $sessionEnd <= $now)) {
                continue;
            }

            /** @var \App\Entity\Skill $tskill */
            $tskill = $session->getSkillTaught();
            /** @var \App\Entity\Skill $rskill */
            $rskill = $exchange->getSkillRequested();
            /** @var \App\Entity\User  $req   */
            $req    = $exchange->getRequester();   // = $user here
            /** @var \App\Entity\User  $acc   */
            $acc    = $exchange->getAttendee();

            $partage[] = [
                'session_id'                  => $session->getId(),
                'date_session'                => $sessionDate->format('Y-m-d'),
                'start_time'                  => $session->getStartTime()->format('H:i:s'),
                'end_time'                    => $sessionEnd->format('H:i:s'),
                'description'                 => $session->getDescription() ?? '',
                'skill_taught_id'             => $tskill->getId(),
                'skill_taught_category_id'    => $tskill->getCategory()->getId(),
                'skill_taught_name'           => $tskill->getName(),
                'skill_requested_id'          => $rskill->getId(),
                'skill_requested_category_id' => $rskill->getCategory()->getId(),
                'skill_requested_name'        => $rskill->getName(),
                'requester_user_id'           => $req->getId(),
                'requester_first_name'        => $req->getFirstName(),
                'requester_last_name'         => $req->getLastName(),
                'requester_avatar'            => $req->getAvatarPath() ?? '',
                'accepter_user_id'            => $acc ? $acc->getId() : null,
                'accepter_first_name'         => $acc?->getFirstName() ?? '',
                'accepter_last_name'          => $acc?->getLastName() ?? '',
                'accepter_avatar'             => $acc?->getAvatarPath() ?? '',
            ];
        }

        // 1.2) Échanges où $user est “attendee”
        foreach ($user->getExchangesAttended() as $exchange) {
            $session = $exchange->getSession();
            if (! $session) {
                continue;
            }

            $sessionDate = $session->getDate();
            $sessionEnd  = $session->getEndTime();
            if ($sessionDate < $today || ($sessionDate == $today && $sessionEnd <= $now)) {
                continue;
            }

            /** @var \App\Entity\Skill $tskill */
            $tskill = $session->getSkillTaught();
            /** @var \App\Entity\Skill $rskill */
            $rskill = $exchange->getSkillRequested();
            /** @var \App\Entity\User  $req   */
            $req    = $exchange->getRequester();
            /** @var \App\Entity\User  $acc   */
            $acc    = $exchange->getAttendee(); // = $user here

            $partage[] = [
                'session_id'                  => $session->getId(),
                'date_session'                => $sessionDate->format('Y-m-d'),
                'start_time'                  => $session->getStartTime()->format('H:i:s'),
                'end_time'                    => $session->getEndTime()->format('H:i:s'),
                'description'                 => $session->getDescription() ?? '',
                'skill_taught_id'             => $tskill->getId(),
                'skill_taught_category_id'    => $tskill->getCategory()->getId(),
                'skill_taught_name'           => $tskill->getName(),
                'skill_requested_id'          => $rskill->getId(),
                'skill_requested_category_id' => $rskill->getCategory()->getId(),
                'skill_requested_name'        => $rskill->getName(),
                'requester_user_id'           => $req->getId(),
                'requester_first_name'        => $req->getFirstName(),
                'requester_last_name'         => $req->getLastName(),
                'requester_avatar'            => $req->getAvatarPath() ?? '',
                'accepter_user_id'            => $acc->getId(),
                'accepter_first_name'         => $acc->getFirstName(),
                'accepter_last_name'          => $acc->getLastName(),
                'accepter_avatar'             => $acc->getAvatarPath() ?? '',
            ];
        }


        // ————————————————
        // 2) Collecte des “cours” (Lesson → Session)
        //    • user est “host” OU est déjà dans “attendees”
        //    • on parcourt BOTH lessonsHosted ET lessonsAttended
        //    • ne garder que les sessions dans le futur
        // ————————————————

        $cours = [];

        // 2.1) Cours hébergés par $user
        foreach ($user->getLessonsHosted() as $lesson) {
            $session = $lesson->getSession();
            if (! $session) {
                continue;
            }

            $sessionDate = $session->getDate();
            $sessionEnd  = $session->getEndTime();
            if ($sessionDate < $today || ($sessionDate == $today && $sessionEnd <= $now)) {
                continue;
            }

            /** @var \App\Entity\Skill $tskill */
            $tskill = $session->getSkillTaught();
            /** @var \App\Entity\Location $loc   */
            $loc    = $lesson->getLocation();

            // Nombre d'inscrits actuels (taille de la collection)
            $currentAttendees = $lesson->getAttendees()->count();

            $cours[] = [
                'session_id'            => $session->getId(),
                'date_session'          => $sessionDate->format('Y-m-d'),
                'start_time'            => $session->getStartTime()->format('H:i:s'),
                'end_time'              => $session->getEndTime()->format('H:i:s'),
                'description'           => $session->getDescription() ?? '',
                'max_attendees'         => $lesson->getMaxAttendees(),
                'current_attendees'     => $currentAttendees,
                'host_user_id'          => $user->getId(),
                'host_first_name'       => $user->getFirstName(),
                'host_last_name'        => $user->getLastName(),
                'host_avatar'           => $user->getAvatarPath() ?? '',
                'skill_taught_id'       => $tskill->getId(),
                'skill_taught_category_id' => $tskill->getCategory()->getId(),
                'skill_taught_name'     => $tskill->getName(),
        'full_address'          => $loc,
            ];
        }

        // 2.2) Cours auxquels $user est déjà inscrit
        foreach ($user->getLessonsAttended() as $lesson) {
            $session = $lesson->getSession();
            if (! $session) {
                continue;
            }

            $sessionDate = $session->getDate();
            $sessionEnd  = $session->getEndTime();
            if ($sessionDate < $today || ($sessionDate == $today && $sessionEnd <= $now)) {
                continue;
            }

            /** @var \App\Entity\Skill $tskill */
            $tskill = $session->getSkillTaught();
            /** @var \App\Entity\User  $host   */
            $host   = $lesson->getHost();
            $loc    = $lesson->getLocation();
            $currentAttendees = $lesson->getAttendees()->count();

            $cours[] = [
                'session_id'            => $session->getId(),
                'date_session'          => $sessionDate->format('Y-m-d'),
                'start_time'            => $session->getStartTime()->format('H:i:s'),
                'end_time'              => $session->getEndTime()->format('H:i:s'),
                'description'           => $session->getDescription() ?? '',
                'max_attendees'         => $lesson->getMaxAttendees(),
                'current_attendees'     => $currentAttendees,
                'host_user_id'          => $host->getId(),
                'host_first_name'       => $host->getFirstName(),
                'host_last_name'        => $host->getLastName(),
                'host_avatar'           => $host->getAvatarPath() ?? '',
                'skill_taught_id'       => $tskill->getId(),
                'skill_taught_category_id' => $tskill->getCategory()->getId(),
                'skill_taught_name'     => $tskill->getName(),
'full_address' => (string) $loc,
            ];
        }

        return [
            'partage' => $partage,
            'cours'   => $cours,
        ];
    }
}
