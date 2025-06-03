<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Lesson;
use App\Entity\Exchange;
use App\Entity\Location;
use App\Entity\Session;
use App\Entity\Skill;
use App\Form\LessonType;
use App\Form\ExchangeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class SessionController extends AbstractController
{
    #[Route('/session/create', name: 'app_session_create')]
    #[IsGranted('ROLE_USER')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // Récupérer toutes les catégories
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('session/createSession.html.twig', [
            'title' => 'Création d\'un cours ou d\'un échange',
            'categories' => $categories,
            'edit_mode' => false,
        ]);
    }
    
    #[Route('/session/edit/{id}/{type}', name: 'app_session_edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(int $id, string $type, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $categories = $em->getRepository(Category::class)->findAll();
        
        if ($type === 'lesson') {
            // Récupérer d'abord la session, puis la leçon associée
        $session = $em->getRepository(Session::class)->find($id);
        $lesson = $session ? $session->getLesson() : null;
            
            if (!$lesson || $lesson->getHost()->getId() !== $user->getId()) {
                $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier ce cours.');
                return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
            }
            
            $session = $lesson->getSession();
            $location = $lesson->getLocation();
            
            return $this->render('session/createSession.html.twig', [
                'title' => 'Modification d\'un cours',
                'categories' => $categories,
                'edit_mode' => true,
                'session_type' => 'lesson',
                'session_id' => $id,
                'session_data' => [
                    'skill_taught_id' => $session->getSkillTaught()->getCategory()->getId(),
                    'description' => $session->getSkillTaught()->getName(),
                    'date_session' => $session->getDate()->format('Y-m-d'),
                    'start_time' => $session->getStartTime()->format('H:i'),
                    'end_time' => $session->getEndTime()->format('H:i'),
                    'address' => $location->getAdress(),
                    'city' => $location->getCity(),
                    'zip_code' => $location->getZipCode(),
                    'max_attendees' => $lesson->getMaxAttendees(),
                ]
            ]);
        } else if ($type === 'exchange') {
            // Récupérer d'abord la session, puis l'échange associé
        $session = $em->getRepository(Session::class)->find($id);
        $exchange = $session ? $session->getExchange() : null;
            
            if (!$exchange || $exchange->getRequester()->getId() !== $user->getId()) {
                $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cet échange.');
                return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
            }
            
            $session = $exchange->getSession();
            
            return $this->render('session/createSession.html.twig', [
                'title' => 'Modification d\'un échange',
                'categories' => $categories,
                'edit_mode' => true,
                'session_type' => 'exchange',
                'session_id' => $id,
                'session_data' => [
                    'skill_taught_id' => $session->getSkillTaught()->getCategory()->getId(),
                    'description' => $session->getSkillTaught()->getName(),
                    'skill_requested_id' => $exchange->getSkillRequested()->getCategory()->getId(),
                    'competence_requested' => $exchange->getSkillRequested()->getName(),
                    'date_session' => $session->getDate()->format('Y-m-d'),
                    'start_time' => $session->getStartTime()->format('H:i'),
                    'end_time' => $session->getEndTime()->format('H:i'),
                ]
            ]);
        } else {
            $this->addFlash('error', 'Type de session non valide.');
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }
    }

    #[Route('/session/create/lesson', name: 'app_course_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createCourse(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Créer une nouvelle location
        $location = new Location();
        $location->setAdress($request->request->get('address'));
        $location->setZipCode($request->request->get('zip_code'));
        $location->setCity($request->request->get('city'));

        $em->persist($location);

        // Créer ou récupérer le skill
        $skillName = $request->request->get('description');
        $categoryId = $request->request->get('skill_taught_id');

        $category = $em->getRepository(Category::class)->find($categoryId);

        $skill = $em->getRepository(Skill::class)->findOneBy([
            'name' => $skillName,
            'category' => $category
        ]);

        if (!$skill) {
            $skill = new Skill();
            $skill->setName($skillName);
            $skill->setCategory($category);
            $skill->setSearchCounter(0);

            $em->persist($skill);
        }

        // Créer la session
        $session = new Session();
        $session->setStartTime(new \DateTime($request->request->get('start_time')));
        $session->setEndTime(new \DateTime($request->request->get('end_time')));
        $session->setDate(new \DateTime($request->request->get('date_session')));
        $rate = $em->getRepository(\App\Entity\Rate::class)->find(1); // 1 est l'ID du tarif par défaut
        $session->setCost($rate);
        $session->setDescription('');
        $session->setSkillTaught($skill);

        $em->persist($session);

        // Créer le cours
        $course = new Lesson();
        $course->setSession($session);
        $course->setLocation($location);
        $course->setHost($user);
        $course->setMaxAttendees((int)$request->request->get('max_attendees'));

        $em->persist($course);

        $em->flush();

        $this->addFlash('success', 'Votre cours a été créé avec succès.');

        return $this->redirectToRoute('home', ['success' => 'cours_cree']);
    }

    #[Route('/session/create/exchange', name: 'app_exchange_create', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function createExchange(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Créer ou récupérer le skill enseigné
        $skillTaughtName = $request->request->get('description');
        $categoryTaughtId = $request->request->get('skill_taught_id');

        $categoryTaught = $em->getRepository(Category::class)->find($categoryTaughtId);

        $skillTaught = $em->getRepository(Skill::class)->findOneBy([
            'name' => $skillTaughtName,
            'category' => $categoryTaught
        ]);

        if (!$skillTaught) {
            $skillTaught = new Skill();
            $skillTaught->setName($skillTaughtName);
            $skillTaught->setCategory($categoryTaught);
            $skillTaught->setSearchCounter(0);

            $em->persist($skillTaught);
        }

        // Créer ou récupérer le skill demandé
        $skillRequestedName = $request->request->get('competence_requested');
        $categoryRequestedId = $request->request->get('skill_requested_id');

        $categoryRequested = $em->getRepository(Category::class)->find($categoryRequestedId);

        $skillRequested = $em->getRepository(Skill::class)->findOneBy([
            'name' => $skillRequestedName,
            'category' => $categoryRequested
        ]);

        if (!$skillRequested) {
            $skillRequested = new Skill();
            $skillRequested->setName($skillRequestedName);
            $skillRequested->setCategory($categoryRequested);
            $skillRequested->setSearchCounter(0);

            $em->persist($skillRequested);
        }

        // Créer la session
        $session = new Session();
        $session->setStartTime(new \DateTime($request->request->get('start_time')));
        $session->setEndTime(new \DateTime($request->request->get('end_time')));
        $session->setDate(new \DateTime($request->request->get('date_session')));
        $session->setDescription('');
        $session->setSkillTaught($skillTaught);
        $rate = $em->getRepository(\App\Entity\Rate::class)->find(1); // 1 est l'ID du tarif par défaut
        $session->setCost($rate);

        $em->persist($session);

        // Créer l'échange
        $exchange = new Exchange();
        $exchange->setSession($session);
        $exchange->setSkillRequested($skillRequested);
        $exchange->setRequester($user);
        // L'accepteur est null au début

        $em->persist($exchange);

        $em->flush();

        $this->addFlash('success', 'Votre échange a été créé avec succès.');

        return $this->redirectToRoute('home', ['success' => 'partage_cree']);
    }
    
    #[Route('/session/update/lesson/{id}', name: 'app_course_update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateCourse(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        // Récupérer d'abord la session, puis la leçon associée
        $session = $em->getRepository(Session::class)->find($id);
        $lesson = $session ? $session->getLesson() : null;
        
        if (!$lesson || $lesson->getHost()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier ce cours.');
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }
        
        $session = $lesson->getSession();
        $location = $lesson->getLocation();
        
        // Mettre à jour la location
        $location->setAdress($request->request->get('address'));
        $location->setZipCode($request->request->get('zip_code'));
        $location->setCity($request->request->get('city'));
        
        // Mettre à jour la compétence enseignée
        $skillName = $request->request->get('description');
        $categoryId = $request->request->get('skill_taught_id');
        $category = $em->getRepository(Category::class)->find($categoryId);
        
        $skill = $em->getRepository(Skill::class)->findOneBy([
            'name' => $skillName,
            'category' => $category
        ]);
        
        if (!$skill) {
            $skill = new Skill();
            $skill->setName($skillName);
            $skill->setCategory($category);
            $skill->setSearchCounter(0);
            
            $em->persist($skill);
        }
        
        // Mettre à jour la session
        $session->setStartTime(new \DateTime($request->request->get('start_time')));
        $session->setEndTime(new \DateTime($request->request->get('end_time')));
        $session->setDate(new \DateTime($request->request->get('date_session')));
        $session->setSkillTaught($skill);
        
        // Mettre à jour le cours
        $lesson->setMaxAttendees((int)$request->request->get('max_attendees'));
        
        $em->flush();
        
        $this->addFlash('success', 'Votre cours a été modifié avec succès.');
        
        return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
    }
    
    #[Route('/session/update/exchange/{id}', name: 'app_exchange_update', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateExchange(int $id, Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        // Récupérer d'abord la session, puis l'échange associé
        $session = $em->getRepository(Session::class)->find($id);
        $exchange = $session ? $session->getExchange() : null;
        
        if (!$exchange || $exchange->getRequester()->getId() !== $user->getId()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à modifier cet échange.');
            return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
        }
        
        $session = $exchange->getSession();
        
        // Mettre à jour la compétence enseignée
        $skillTaughtName = $request->request->get('description');
        $categoryTaughtId = $request->request->get('skill_taught_id');
        $categoryTaught = $em->getRepository(Category::class)->find($categoryTaughtId);
        
        $skillTaught = $em->getRepository(Skill::class)->findOneBy([
            'name' => $skillTaughtName,
            'category' => $categoryTaught
        ]);
        
        if (!$skillTaught) {
            $skillTaught = new Skill();
            $skillTaught->setName($skillTaughtName);
            $skillTaught->setCategory($categoryTaught);
            $skillTaught->setSearchCounter(0);
            
            $em->persist($skillTaught);
        }
        
        // Mettre à jour la compétence demandée
        $skillRequestedName = $request->request->get('competence_requested');
        $categoryRequestedId = $request->request->get('skill_requested_id');
        $categoryRequested = $em->getRepository(Category::class)->find($categoryRequestedId);
        
        $skillRequested = $em->getRepository(Skill::class)->findOneBy([
            'name' => $skillRequestedName,
            'category' => $categoryRequested
        ]);
        
        if (!$skillRequested) {
            $skillRequested = new Skill();
            $skillRequested->setName($skillRequestedName);
            $skillRequested->setCategory($categoryRequested);
            $skillRequested->setSearchCounter(0);
            
            $em->persist($skillRequested);
        }
        
        // Mettre à jour la session
        $session->setStartTime(new \DateTime($request->request->get('start_time')));
        $session->setEndTime(new \DateTime($request->request->get('end_time')));
        $session->setDate(new \DateTime($request->request->get('date_session')));
        $session->setSkillTaught($skillTaught);
        
        // Mettre à jour l'échange
        $exchange->setSkillRequested($skillRequested);
        
        $em->flush();
        
        $this->addFlash('success', 'Votre échange a été modifié avec succès.');
        
        return $this->redirectToRoute('app_user_profile', ['id' => $user->getId()]);
    }

    #[Route('/session/clone/{id}/{type}', name: 'app_session_clone')]
    #[IsGranted('ROLE_USER')]
    public function clone(int $id, string $type, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $categories = $em->getRepository(Category::class)->findAll();
        
        if ($type === 'lesson') {
            $session = $em->getRepository(Session::class)->find($id);
            $lesson = $session ? $session->getLesson() : null;
            
            if (!$lesson) {
                $this->addFlash('error', 'Session introuvable.');
                return $this->redirectToRoute('home');
            }
            
            // Vérification de propriété : seul le créateur peut cloner
            if ($lesson->getHost()->getId() !== $user->getId()) {
                $this->addFlash('error', 'Vous n\'êtes pas autorisé à cloner ce cours.');
                return $this->redirectToRoute('home');
            }
            
            $location = $lesson->getLocation();
            
            return $this->render('session/createSession.html.twig', [
                'title' => 'Clonage d\'un cours',
                'categories' => $categories,
                'edit_mode' => false,
                'clone_mode' => true,
                'session_type' => 'lesson',
                'session_data' => [
                    'skill_taught_id' => $session->getSkillTaught()->getCategory()->getId(),
                    'description' => $session->getSkillTaught()->getName(),
                    'address' => $location->getAdress(),
                    'city' => $location->getCity(),
                    'zip_code' => $location->getZipCode(),
                    'max_attendees' => $lesson->getMaxAttendees(),
                ]
            ]);
        } else if ($type === 'exchange') {
            $session = $em->getRepository(Session::class)->find($id);
            $exchange = $session ? $session->getExchange() : null;
            
            if (!$exchange) {
                $this->addFlash('error', 'Session introuvable.');
                return $this->redirectToRoute('home');
            }
            
            // Vérification de propriété : seul le créateur peut cloner
            if ($exchange->getRequester()->getId() !== $user->getId()) {
                $this->addFlash('error', 'Vous n\'êtes pas autorisé à cloner cet échange.');
                return $this->redirectToRoute('home');
            }
            
            return $this->render('session/createSession.html.twig', [
                'title' => 'Clonage d\'un échange',
                'categories' => $categories,
                'edit_mode' => false,
                'clone_mode' => true,
                'session_type' => 'exchange',
                'session_data' => [
                    'skill_taught_id' => $session->getSkillTaught()->getCategory()->getId(),
                    'description' => $session->getSkillTaught()->getName(),
                    'skill_requested_id' => $exchange->getSkillRequested()->getCategory()->getId(),
                    'competence_requested' => $exchange->getSkillRequested()->getName(),
                ]
            ]);
        } else {
            $this->addFlash('error', 'Type de session non valide.');
            return $this->redirectToRoute('home');
        }
    }
}