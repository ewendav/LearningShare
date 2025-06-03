<?php

namespace App\Service;

use App\Entity\Review;
use App\Entity\User;
use App\Entity\Session;
use Doctrine\ORM\EntityManagerInterface;

class RatingService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function getAverageRating(User $user): float
    {
        $reviews = $this->entityManager
            ->getRepository(Review::class)
            ->findBy(['reviewReceiver' => $user]);

        if (empty($reviews)) {
            return 0.0;
        }

        $totalRating = 0;
        foreach ($reviews as $review) {
            $totalRating += $review->getRating();
        }

        return round($totalRating / count($reviews), 1);
    }

    public function canUserRate(User $rater, User $userToRate): bool
    {
        if ($rater->getId() === $userToRate->getId()) {
            return false;
        }

        $existingReview = $this->entityManager
            ->getRepository(Review::class)
            ->findOneBy([
                'reviewGiver' => $rater,
                'reviewReceiver' => $userToRate
            ]);

        if ($existingReview) {
            return false;
        }

        return $this->hasParticipatedInSessionWith($rater, $userToRate);
    }

    private function hasParticipatedInSessionWith(User $rater, User $userToRate): bool
    {
        $now = new \DateTime();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->select('COUNT(s.id)')
            ->from(Session::class, 's')
            ->leftJoin('s.lesson', 'l')
            ->leftJoin('l.attendees', 'la')
            ->leftJoin('s.exchange', 'e')
            ->where('s.date < :now')
            ->setParameter('now', $now)
            ->andWhere(
                $queryBuilder->expr()->orX(
                    // Cas 1: rater a participé à un cours hébergé par userToRate
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->isNotNull('l.id'),
                        $queryBuilder->expr()->eq('l.host', ':userToRate'),
                        $queryBuilder->expr()->eq('la.id', ':rater')
                    ),
                    // Cas 2: userToRate a participé à un cours hébergé par rater
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->isNotNull('l.id'),
                        $queryBuilder->expr()->eq('l.host', ':rater'),
                        $queryBuilder->expr()->eq('la.id', ':userToRate')
                    ),
                    // Cas 3: rater était demandeur, userToRate était participant dans un échange
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->isNotNull('e.id'),
                        $queryBuilder->expr()->eq('e.requester', ':rater'),
                        $queryBuilder->expr()->eq('e.attendee', ':userToRate')
                    ),
                    // Cas 4: userToRate était demandeur, rater était participant dans un échange
                    $queryBuilder->expr()->andX(
                        $queryBuilder->expr()->isNotNull('e.id'),
                        $queryBuilder->expr()->eq('e.requester', ':userToRate'),
                        $queryBuilder->expr()->eq('e.attendee', ':rater')
                    )
                )
            )
            ->setParameter('rater', $rater)
            ->setParameter('userToRate', $userToRate);

        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        return $count > 0;
    }

    public function createReview(User $reviewer, User $reviewedUser, int $rating): Review
    {
        $review = new Review();
        $review->setReviewGiver($reviewer);
        $review->setReviewReceiver($reviewedUser);
        $review->setRating($rating);
        $review->setContent('');

        $this->entityManager->persist($review);
        $this->entityManager->flush();

        return $review;
    }
}