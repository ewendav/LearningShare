<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Exchange;
use App\Entity\User;
use App\Entity\Session;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Psr\Log\LoggerInterface;

class JoinController extends AbstractController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/rejoindreCours/{id}', name: 'app_join_lesson', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function joinLesson(int $id, EntityManagerInterface $em, Request $request): Response
    {

        $this->logger->info("Tentative de rejoindre le cours", ['session_id' => $id, 'user_id' => $this->getUser()->getId()]);

        // Récupérer la session
        $session = $em->getRepository(Session::class)->find($id);

        if (!$session || !$session->getLesson()) {
            $this->logger->error("Cours non trouvé", ['session_id' => $id]);
            $this->addFlash('error', 'Cours non trouvé');
            return $this->redirectToRoute('home');
        }

        // Récupérer le cours associé à cette session
        $lesson = $session->getLesson();

        // Vérifier si l'utilisateur n'est pas déjà inscrit
        /** @var User $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur est l'hôte du cours
        if ($lesson->getHost() === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas rejoindre votre propre cours');
            return $this->redirectToRoute('home');
        }

        // Vérifier si l'utilisateur est déjà inscrit
        if ($user->getLessonsAttended()->contains($lesson)) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à ce cours');
            return $this->redirectToRoute('home');
        }

        // Vérifier si le cours est complet
        $attendeesCount = $lesson->getAttendees()->count();

        if ($attendeesCount >= $lesson->getMaxAttendees()) {
            $this->addFlash('error', 'Ce cours est complet');
            return $this->redirectToRoute('home');
        }

        // Vérifier si l'utilisateur a suffisamment de jetons (25 requis)
        if ($user->getBalance() < 25) {
            $this->addFlash('error', 'Vous n\'avez pas assez de jetons (25 jetons requis)');
            return $this->redirectToRoute('home');
        }

        try {
            // Débuter une transaction
            $em->beginTransaction();

            // Ajouter l'utilisateur aux participants du cours
            // Utiliser uniquement la méthode lesson->addAttendee pour établir la relation bidirectionnelle
            // car cette méthode appelle déjà user->addLessonsAttended en interne
            $lesson->addAttendee($user);

            // Récupérer l'hôte du cours
            $host = $lesson->getHost();

            // Déduire 25 jetons de l'utilisateur
            $user->setBalance($user->getBalance() - 25);

            // Ajouter 20 jetons à l'hôte
            $host->setBalance($host->getBalance() + 20);

            $em->persist($user);
            $em->persist($host);
            $em->flush();
            $em->commit();

            $this->logger->info("Utilisateur a rejoint le cours avec succès", [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'tokens_deducted' => 25,
                'host_id' => $host->getId(),
                'tokens_added_to_host' => 20
            ]);

            $this->addFlash('success', 'Vous avez rejoint le cours avec succès');
        } catch (\Exception $e) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollback();
            }

            $this->logger->error("Échec pour rejoindre le cours: " . $e->getMessage(), [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'exception' => $e->getMessage()
            ]);

            $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription au cours');
        }

        return $this->redirectToRoute('home');
    }

    #[Route('/rejoindrePartage/{id}', name: 'app_join_exchange', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function joinExchange(int $id, EntityManagerInterface $em, Request $request): Response
    {

        $this->logger->info("Tentative de rejoindre le partage", ['session_id' => $id, 'user_id' => $this->getUser()->getId()]);

        // Récupérer la session
        $session = $em->getRepository(Session::class)->find($id);

        if (!$session || !$session->getExchange()) {
            $this->logger->error("Partage non trouvé", ['session_id' => $id]);
            $this->addFlash('error', 'Partage non trouvé');
            return $this->redirectToRoute('home');
        }

        // Récupérer l'échange associé à cette session
        $exchange = $session->getExchange();

        // Vérifier si l'utilisateur n'est pas le créateur du partage
        /** @var User $user */
        $user = $this->getUser();

        if ($exchange->getRequester() === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas rejoindre votre propre partage');
            return $this->redirectToRoute('home');
        }

        // Vérifier que le partage n'a pas déjà été accepté
        if ($exchange->getAttendee()) {
            $this->addFlash('error', 'Ce partage a déjà été accepté');
            return $this->redirectToRoute('home');
        }

        try {
            // Débuter une transaction
            $em->beginTransaction();

            // Récupérer le créateur du partage
            $requester = $exchange->getRequester();

            // Définir l'utilisateur comme accepteur du partage
            $exchange->setAttendee($user);
            // S'assurer que la relation inverse est également mise à jour
            $user->addExchangesAttended($exchange);

            // Ajouter 40 jetons à l'utilisateur qui rejoint le partage
            $user->setBalance($user->getBalance() + 40);

            // Ajouter 40 jetons au créateur du partage
            $requester->setBalance($requester->getBalance() + 40);

            $em->persist($exchange);
            $em->persist($user);
            $em->persist($requester);
            $em->flush();
            $em->commit();

            $this->logger->info("Utilisateur a rejoint le partage avec succès", [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'tokens_added_to_user' => 40,
                'requester_id' => $requester->getId(),
                'tokens_added_to_requester' => 40
            ]);

            $this->addFlash('success', 'Vous avez rejoint le partage avec succès');
        } catch (\Exception $e) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollback();
            }

            $this->logger->error("Échec pour rejoindre le partage: " . $e->getMessage(), [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'exception' => $e->getMessage()
            ]);

            $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription au partage');
        }

        return $this->redirectToRoute('home');
    }
    
    #[Route('/quitterCours/{id}', name: 'app_leave_lesson', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function leaveLesson(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $this->logger->info("Tentative de quitter le cours", ['session_id' => $id, 'user_id' => $this->getUser()->getId()]);

        // Récupérer la session
        $session = $em->getRepository(Session::class)->find($id);

        if (!$session || !$session->getLesson()) {
            $this->logger->error("Cours non trouvé", ['session_id' => $id]);
            $this->addFlash('error', 'Cours non trouvé');
            return $this->redirectToRoute('app_user_profile', ['id' => $this->getUser()->getId()]);
        }

        // Récupérer le cours associé à cette session
        $lesson = $session->getLesson();

        /** @var User $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur est bien inscrit
        if (!$user->getLessonsAttended()->contains($lesson)) {
            $this->addFlash('error', 'Vous n\'êtes pas inscrit à ce cours');
            return $this->redirectToRoute('app_user_profile', ['id' => $this->getUser()->getId()]);
        }

        try {
            // Débuter une transaction
            $em->beginTransaction();

            // Retirer l'utilisateur des participants du cours
            $lesson->removeAttendee($user);
            
            // Récupérer l'hôte du cours
            $host = $lesson->getHost();

            // Rendre 15 jetons à l'utilisateur
            $user->setBalance($user->getBalance() + 15);

            // Retirer 10 jetons à l'hôte
            $host->setBalance($host->getBalance() - 10);

            $em->persist($user);
            $em->persist($host);
            $em->persist($lesson);
            $em->flush();
            $em->commit();

            $this->logger->info("Utilisateur a quitté le cours avec succès", [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'tokens_refunded' => 15,
                'host_id' => $host->getId(),
                'tokens_removed_from_host' => 10
            ]);

            $this->addFlash('success', 'Vous avez quitté le cours avec succès');
        } catch (\Exception $e) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollback();
            }

            $this->logger->error("Échec pour quitter le cours: " . $e->getMessage(), [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'exception' => $e->getMessage()
            ]);

            $this->addFlash('error', 'Une erreur est survenue lors de la désinscription au cours');
        }

        // Rediriger vers le profil de l'utilisateur
        return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
    }
    
    #[Route('/quitterPartage/{id}', name: 'app_leave_exchange', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function leaveExchange(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $this->logger->info("Tentative de quitter le partage", ['session_id' => $id, 'user_id' => $this->getUser()->getId()]);

        // Récupérer la session
        $session = $em->getRepository(Session::class)->find($id);

        if (!$session || !$session->getExchange()) {
            $this->logger->error("Partage non trouvé", ['session_id' => $id]);
            $this->addFlash('error', 'Partage non trouvé');
            return $this->redirectToRoute('app_user_profile', ['id' => $this->getUser()->getId()]);
        }

        // Récupérer l'échange associé à cette session
        $exchange = $session->getExchange();

        /** @var User $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur est bien le participant
        if ($exchange->getAttendee() !== $user) {
            $this->addFlash('error', 'Vous n\'êtes pas participant à ce partage');
            return $this->redirectToRoute('app_user_profile', ['id' => $this->getUser()->getId()]);
        }

        try {
            // Débuter une transaction
            $em->beginTransaction();

            // Récupérer le créateur du partage
            $requester = $exchange->getRequester();

            // Retirer l'utilisateur du partage
            $exchange->setAttendee(null);
            
            // Retirer 30 jetons à l'utilisateur qui quitte le partage
            $user->setBalance($user->getBalance() - 30);

            // Retirer 30 jetons au créateur du partage
            $requester->setBalance($requester->getBalance() - 30);

            $em->persist($exchange);
            $em->persist($user);
            $em->persist($requester);
            $em->flush();
            $em->commit();

            $this->logger->info("Utilisateur a quitté le partage avec succès", [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'tokens_removed_from_user' => 30,
                'requester_id' => $requester->getId(),
                'tokens_removed_from_requester' => 30
            ]);

            $this->addFlash('success', 'Vous avez quitté le partage avec succès');
        } catch (\Exception $e) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollback();
            }

            $this->logger->error("Échec pour quitter le partage: " . $e->getMessage(), [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'exception' => $e->getMessage()
            ]);

            $this->addFlash('error', 'Une erreur est survenue lors de la désinscription au partage');
        }

        // Rediriger vers le profil de l'utilisateur
        return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
    }
    
    #[Route('/annulerCours/{id}', name: 'app_cancel_lesson', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancelLesson(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $this->logger->info("Tentative d'annuler le cours", ['session_id' => $id, 'user_id' => $this->getUser()->getId()]);

        // Récupérer la session
        $session = $em->getRepository(Session::class)->find($id);

        if (!$session || !$session->getLesson()) {
            $this->logger->error("Cours non trouvé", ['session_id' => $id]);
            $this->addFlash('error', 'Cours non trouvé');
            return $this->redirectToRoute('app_user_profile', ['id' => $this->getUser()->getId()]);
        }

        // Récupérer le cours associé à cette session
        $lesson = $session->getLesson();

        /** @var User $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur est bien l'hôte du cours
        if ($lesson->getHost() !== $user) {
            $this->addFlash('error', 'Vous n\'êtes pas l\'hôte de ce cours');
            return $this->redirectToRoute('app_user_profile', ['id' => $this->getUser()->getId()]);
        }

        try {
            // Débuter une transaction
            $em->beginTransaction();

            // Si le cours a des participants, les rembourser
            foreach ($lesson->getAttendees() as $attendee) {
                // Rembourser 25 jetons à chaque participant
                $attendee->setBalance($attendee->getBalance() + 25);
                
                // Retirer 20 jetons à l'hôte pour chaque participant
                $user->setBalance($user->getBalance() - 20);
                
                $em->persist($attendee);
            }

            // Sauvegarder les modifications de l'hôte
            $em->persist($user);

            // Récupérer l'emplacement pour le supprimer également
            $location = $lesson->getLocation();

            // Détacher les relations
            $session->setLesson(null);
            $em->persist($session);
            $em->flush(); // Flush intermediaire pour éviter les erreurs de contraintes

            // Supprimer le cours
            $em->remove($lesson);
            
            // Supprimer la session
            $em->remove($session);
            
            // Supprimer l'emplacement s'il existe
            if ($location) {
                $em->remove($location);
            }

            $em->flush();
            $em->commit();

            $this->logger->info("Cours annulé avec succès", [
                'session_id' => $id,
                'host_id' => $user->getId()
            ]);

            $this->addFlash('success', 'Votre cours a été annulé avec succès');
        } catch (\Exception $e) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollback();
            }

            $this->logger->error("Échec pour annuler le cours: " . $e->getMessage(), [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'exception' => $e->getMessage()
            ]);

            $this->addFlash('error', 'Une erreur est survenue lors de l\'annulation du cours');
        }

        // Rediriger vers le profil de l'utilisateur
        return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
    }
    
    #[Route('/annulerPartage/{id}', name: 'app_cancel_exchange', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancelExchange(int $id, EntityManagerInterface $em, Request $request): Response
    {
        $this->logger->info("Tentative d'annuler le partage", ['session_id' => $id, 'user_id' => $this->getUser()->getId()]);

        // Récupérer la session
        $session = $em->getRepository(Session::class)->find($id);

        if (!$session || !$session->getExchange()) {
            $this->logger->error("Partage non trouvé", ['session_id' => $id]);
            $this->addFlash('error', 'Partage non trouvé');
            return $this->redirectToRoute('app_user_profile', ['id' => $this->getUser()->getId()]);
        }

        // Récupérer l'échange associé à cette session
        $exchange = $session->getExchange();

        /** @var User $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur est bien le créateur du partage
        if ($exchange->getRequester() !== $user) {
            $this->addFlash('error', 'Vous n\'êtes pas le créateur de ce partage');
            return $this->redirectToRoute('app_user_profile', ['id' => $this->getUser()->getId()]);
        }

        try {
            // Débuter une transaction
            $em->beginTransaction();

            // Si le partage a un participant, le rembourser
            $attendee = $exchange->getAttendee();
            if ($attendee) {
                // Retirer 40 jetons au participant
                $attendee->setBalance($attendee->getBalance() - 40);
                
                // Retirer 40 jetons au créateur du partage
                $user->setBalance($user->getBalance() - 40);
                
                $em->persist($attendee);
            }

            // Sauvegarder les modifications du créateur
            $em->persist($user);

            // Détacher les relations
            $session->setExchange(null);
            $em->persist($session);
            $em->flush(); // Flush intermédiaire pour éviter les erreurs de contraintes

            // Supprimer l'échange
            $em->remove($exchange);
            
            // Supprimer la session
            $em->remove($session);

            $em->flush();
            $em->commit();

            $this->logger->info("Partage annulé avec succès", [
                'session_id' => $id,
                'requester_id' => $user->getId()
            ]);

            $this->addFlash('success', 'Votre partage a été annulé avec succès');
        } catch (\Exception $e) {
            if ($em->getConnection()->isTransactionActive()) {
                $em->rollback();
            }

            $this->logger->error("Échec pour annuler le partage: " . $e->getMessage(), [
                'session_id' => $id,
                'user_id' => $user->getId(),
                'exception' => $e->getMessage()
            ]);

            $this->addFlash('error', 'Une erreur est survenue lors de l\'annulation du partage');
        }

        // Rediriger vers le profil de l'utilisateur
        return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
    }
}