<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile')]
    public function profile(
        Request                $request,
        EntityManagerInterface $em,
        CategoryRepository     $categoryRepository,
        int                    $profileId = null
    ): Response
    {
        $userId = $request->query->get('user_id');
        if (!$userId) {
            $userId = $this->getUser() ? $this->getUser()->getId() : null;
        }
        $type = $request->query->get('type', 'cours'); // Par défaut, afficher les cours

        // Vérification de l'ID utilisateur
        if (!$userId || !is_numeric($userId)) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('home');
        }

        // Récupération des données utilisateur
        $user = $em->getRepository(User::class)->find($userId);
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('home');
        }

        // Récupération des sessions selon le type
        $sessions = [];
        if ($type === 'cours') {
            // Récupérer les cours hébergés par l'utilisateur et ceux auxquels il participe qui ne sont pas expirés
            $sessions = $this->getUsersLessons($em, $user);

        } elseif ($type === 'echange' || $type === 'echanges') {
            // Récupérer les partages créés par l'utilisateur et ceux auxquels il participe qui ne sont pas expirés
            $sessions = $this->getExchangesForUser($em, $user);
        }

        // Récupération de toutes les catégories indexées par ID
        $categories = $categoryRepository->findAll();
        $categoriesIndexed = [];
        foreach ($categories as $category) {
            $categoriesIndexed[$category->getId()] = [
                'id' => $category->getId(),
                'name' => $category->getName()
            ];
        }

        return $this->render('profile/userProfil.html.twig', [
            'title' => 'Profil de ' . $user->getFirstname(),
            'userData' => [
                'user_id' => $user->getId(),
                'user_first_name' => $user->getFirstname(),
                'user_last_name' => $user->getLastname(),
                'biography' => $user->getBiography(),
                'avatar_path' => $user->getAvatarPath()
            ],
            'sessions' => $sessions,
            'categoriesIndexed' => $categoriesIndexed,
            'getParams' => ['type' => $type]
        ]);
    }

    /**
     * Récupère les cours hébergés par un utilisateur ou auxquels il participe qui ne sont pas expirés
     */
    private function getUsersLessons(EntityManagerInterface $em, User $user): array
    {
        // Récupérer les leçons hébergées par l'utilisateur
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select(
                's.id as session_id',
                's.startTime as start_time',
                's.endTime as end_time',
                's.date as date_session',
                's.description',
                'l.maxAttendees as max_attendees',
                'u.firstname as host_first_name',
                'u.lastname as host_last_name',
                'u.avatarPath as host_avatar',
                'u.id as host_user_id',
                '\'hôte\' as user_role',
                'st.id as skill_taught_id',
                'stc.id as skill_taught_category_id',
                'st.name as skill_taught_name',
                'CONCAT(COALESCE(loc.adress, \'\'), \', \', 
  COALESCE(loc.zipCode, \'\'), \', \', COALESCE(loc.city, \'\')) as 
  full_address'
            )
            ->from('App\Entity\Lesson', 'l')
            ->join('l.session', 's')
            ->join('l.host', 'u')
            ->join('s.skillTaught', 'st')
            ->join('st.category', 'stc')
            ->leftJoin('l.location', 'loc')
            ->where('l.host = :user')
            // Temporairement désactivé pour vérifier si des sessions existent
            // ->andWhere('CONCAT(s.date, \' \', s.endTime) > CURRENT_TIMESTAMP()')
            ->setParameter('user', $user);

        $result1 = $queryBuilder->getQuery()->getArrayResult();
        dump("Nombre de leçons hébergées récupérées: " . count($result1));

        // Récupérer les leçons auxquelles l'utilisateur participe
        $queryBuilder2 = $em->createQueryBuilder();
        $queryBuilder2
            ->select(
                's.id as session_id',
                's.startTime as start_time',
                's.endTime as end_time',
                's.date as date_session',
                's.description',
                'l.maxAttendees as max_attendees',
                'host.firstname as host_first_name',
                'host.lastname as host_last_name',
                'host.avatarPath as host_avatar',
                'host.id as host_user_id',
                '\'participant\' as user_role',
                'st.id as skill_taught_id',
                'stc.id as skill_taught_category_id',
                'st.name as skill_taught_name',
                'CONCAT(COALESCE(loc.adress, \'\'), \', \', 
  COALESCE(loc.zipCode, \'\'), \', \', COALESCE(loc.city, \'\')) as 
  full_address'
            )
            ->from('App\Entity\Lesson', 'l')
            ->join('l.session', 's')
            ->join('l.host', 'host')
            ->join('l.attendees', 'att')
            ->join('s.skillTaught', 'st')
            ->join('st.category', 'stc')
            ->leftJoin('l.location', 'loc')
            ->where('att.id = :userId')
            // Temporairement désactivé pour vérifier si des sessions existent
            // ->andWhere('CONCAT(s.date, \' \', s.endTime) > CURRENT_TIMESTAMP()')
            ->setParameter('userId', $user->getId());

        $result2 = $queryBuilder2->getQuery()->getArrayResult();
        dump("Nombre de leçons attendues récupérées: " . count($result2));

        // Union des deux requêtes
        $result = array_merge($result1, $result2);

        // Convertir les objets DateTime en chaînes formatées
        foreach ($result as &$row) {
            if (isset($row['start_time']) && $row['start_time'] instanceof \DateTime) {
                $row['start_time'] = $row['start_time']->format('H:i');
            }
            if (isset($row['end_time']) && $row['end_time'] instanceof \DateTime) {
                $row['end_time'] = $row['end_time']->format('H:i');
            }
            if (isset($row['date_session']) && $row['date_session'] instanceof \DateTime) {
                $row['date_session'] = $row['date_session']->format('Y-m-d');
            }
        }

        // Tri par date et heure de début
        usort($result, function ($a, $b) {
            $dateA = $a['date_session'];
            $dateB = $b['date_session'];

            if ($dateA == $dateB) {
                return $a['start_time'] <=> $b['start_time'];
            }

            return $dateA <=> $dateB;
        });

        return $result;
    }
    private function getExchangesForUser(EntityManagerInterface $em, User $user): array
    {
        // Récupérer les partages créés par l'utilisateur
        $queryBuilder = $em->createQueryBuilder();
        $queryBuilder
            ->select(
                's.id as session_id',
                's.startTime as start_time',
                's.endTime as end_time',
                's.date as date_session',
                's.description',
                'st.id as skill_taught_id',
                'stc.id as skill_taught_category_id',
                'st.name as skill_taught_name',
                'sr.id as skill_requested_id',
                'src.id as skill_requested_category_id',
                'sr.name as skill_requested_name',
                'req.id as requester_user_id',
                'req.firstname as requester_first_name',
                'req.lastname as requester_last_name',
                'req.avatarPath as requester_avatar',
                'att.firstname as accepter_first_name',
                'att.lastname as accepter_last_name',
                'att.avatarPath as accepter_avatar',
                '\'demandeur\' as user_role'
            )
            ->from('App\Entity\Exchange', 'e')
            ->join('e.session', 's')
            ->join('e.requester', 'req')
            ->leftJoin('e.attendee', 'att')
            ->join('s.skillTaught', 'st')
            ->join('st.category', 'stc')
            ->join('e.skillRequested', 'sr')
            ->join('sr.category', 'src')
            ->where('e.requester = :user')
            // Temporairement désactivé pour vérifier si des sessions existent
            // ->andWhere('CONCAT(s.date, \' \', s.endTime) > CURRENT_TIMESTAMP()')
            ->setParameter('user', $user);

        $result1 = $queryBuilder->getQuery()->getArrayResult();
        dump("Nombre d'échanges créés récupérés: " . count($result1));

        // Récupérer les partages auxquels l'utilisateur participe
        $queryBuilder2 = $em->createQueryBuilder();
        $queryBuilder2
            ->select(
                's.id as session_id',
                's.startTime as start_time',
                's.endTime as end_time',
                's.date as date_session',
                's.description',
                'st.id as skill_taught_id',
                'stc.id as skill_taught_category_id',
                'st.name as skill_taught_name',
                'sr.id as skill_requested_id',
                'src.id as skill_requested_category_id',
                'sr.name as skill_requested_name',
                'req.id as requester_user_id',
                'req.firstname as requester_first_name',
                'req.lastname as requester_last_name',
                'req.avatarPath as requester_avatar',
                'att.firstname as accepter_first_name',
                'att.lastname as accepter_last_name',
                'att.avatarPath as accepter_avatar',
                '\'participant\' as user_role'
            )
            ->from('App\Entity\Exchange', 'e')
            ->join('e.session', 's')
            ->join('e.requester', 'req')
            ->join('e.attendee', 'att')
            ->join('s.skillTaught', 'st')
            ->join('st.category', 'stc')
            ->join('e.skillRequested', 'sr')
            ->join('sr.category', 'src')
            ->where('e.attendee = :user')
            // Temporairement désactivé pour vérifier si des sessions existent
            // ->andWhere('CONCAT(s.date, \' \', s.endTime) > CURRENT_TIMESTAMP()')
            ->setParameter('user', $user);

        $result2 = $queryBuilder2->getQuery()->getArrayResult();
        dump("Nombre d'échanges attendus récupérés: " . count($result2));

        // Union des deux requêtes
        $result = array_merge($result1, $result2);

        // Convertir les objets DateTime en chaînes formatées
        foreach ($result as &$row) {
            if (isset($row['start_time']) && $row['start_time'] instanceof
                \DateTime) {
                $row['start_time'] = $row['start_time']->format('H:i');
            }
            if (isset($row['end_time']) && $row['end_time'] instanceof
                \DateTime) {
                $row['end_time'] = $row['end_time']->format('H:i');
            }
            if (isset($row['date_session']) && $row['date_session']
                instanceof \DateTime) {
                $row['date_session'] = $row['date_session']->format('Y-m-d');
            }
        }

        // Tri par date et heure de début
        usort($result, function ($a, $b) {
            $dateA = $a['date_session'];
            $dateB = $b['date_session'];

            if ($dateA == $dateB) {
                return $a['start_time'] <=> $b['start_time'];
            }

            return $dateA <=> $dateB;
        });

        return $result;
    }
}