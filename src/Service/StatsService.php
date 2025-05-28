<?php

namespace App\Service;

use App\Entity\Skill;
use App\Entity\Lesson;
use App\Entity\Exchange;
use Doctrine\ORM\EntityManagerInterface;

class StatsService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function getTopSkills(int $limit = 5): array
    {
        $dql = <<<'DQL'
            SELECT s.name AS name, s.searchCounter AS counter
            FROM App\Entity\Skill s
            ORDER BY s.searchCounter DESC, s.name ASC
        DQL;
        return $this->em->createQuery($dql)
            ->setMaxResults($limit)
            ->getArrayResult();
    }

    public function getTopTeachersLast6Months(): array
    {
        $sixMonthsAgo = new \DateTimeImmutable('-6 months');

        // DQL pour les cours
        $lessonDql = '
        SELECT u.id AS userId, COUNT(l.id) AS lessonCount
        FROM App\Entity\User u
        JOIN u.lessonsHosted l
        JOIN l.session s
        WHERE s.date >= :sixMonthsAgo
        GROUP BY u.id
    ';

        $lessonResults = $this->em->createQuery($lessonDql)
            ->setParameter('sixMonthsAgo', $sixMonthsAgo)
            ->getResult();

        // DQL pour les échanges
        $exchangeDql = '
        SELECT u.id AS userId, COUNT(e.id) AS exchangeCount
        FROM App\Entity\User u
        LEFT JOIN App\Entity\Exchange e WITH e.requester = u OR e.attendee = u
        LEFT JOIN e.session s
        WHERE s.date >= :sixMonthsAgo
        GROUP BY u.id
    ';

        $exchangeResults = $this->em->createQuery($exchangeDql)
            ->setParameter('sixMonthsAgo', $sixMonthsAgo)
            ->getResult();

        // Fusion des scores
        $scores = [];

        foreach ($lessonResults as $row) {
            $scores[$row['userId']] = $row['lessonCount'];
        }

        foreach ($exchangeResults as $row) {
            $scores[$row['userId']] = ($scores[$row['userId']] ?? 0) + $row['exchangeCount'];
        }

        // Tri décroissant
        arsort($scores);
        $topIds = array_slice(array_keys($scores), 0, 5);

        if (empty($topIds)) {
            return [];
        }

        // Récupérer les objets User
        $userDql = '
        SELECT u
        FROM App\Entity\User u
        WHERE u.id IN (:ids)
    ';

        $users = $this->em->createQuery($userDql)
            ->setParameter('ids', $topIds)
            ->getResult();

        // Replacer les utilisateurs dans le bon ordre
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user->getId()] = $user;
        }

        $result = [];
        foreach ($topIds as $id) {
            if (isset($userMap[$id])) {
                $result[] = [
                    'user' => $userMap[$id],
                    'activityScore' => $scores[$id],
                ];
            }
        }

        return $result;
    }

    public function getMostTaughtSkills(): array
    {
        $dql = <<<DQL
            SELECT s.name AS skillName, COUNT(sess.id) AS usageCount
            FROM App\Entity\Session sess
            JOIN sess.skillTaught s
            GROUP BY s.id, s.name
            ORDER BY usageCount DESC, s.name ASC
        DQL;

        return $this->em->createQuery($dql)
            ->setMaxResults(5)
            ->getResult();
    }
}

