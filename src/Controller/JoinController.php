<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Exchange;
use App\Entity\User;
use App\Entity\Session;
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
}