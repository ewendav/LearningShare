<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration pour ajouter un jeu de données cohérent pour démonstration
 */
final class Version20250601134212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute un jeu de données cohérent pour démonstration avec 10 utilisateurs, sessions et reviews';
    }

    public function up(Schema $schema): void
    {
        // Nettoyer les données existantes
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('TRUNCATE TABLE user_lesson');
        $this->addSql('TRUNCATE TABLE review');
        $this->addSql('TRUNCATE TABLE report');
        $this->addSql('TRUNCATE TABLE session');
        $this->addSql('TRUNCATE TABLE exchange');
        $this->addSql('TRUNCATE TABLE lesson');
        $this->addSql('TRUNCATE TABLE skill');
        $this->addSql('TRUNCATE TABLE `user`');
        $this->addSql('TRUNCATE TABLE location');
        $this->addSql('TRUNCATE TABLE rate');
        $this->addSql('TRUNCATE TABLE role');
        $this->addSql('TRUNCATE TABLE category');
        $this->addSql('DROP TABLE IF EXISTS blacklist');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');

        // 1. Categories
        $this->addSql("INSERT INTO category (id, name) VALUES 
            (1, 'Informatique'),
            (2, 'Langues'),
            (3, 'Musique'),
            (4, 'Sport'),
            (5, 'Cuisine'),
            (6, 'Sciences'),
            (7, 'Arts')");

        // 2. Skills
        $this->addSql("INSERT INTO skill (id, category_id, name, search_counter) VALUES
            (1, 1, 'PHP', 25),
            (2, 1, 'JavaScript', 30),
            (3, 1, 'Python', 20),
            (4, 2, 'Anglais', 15),
            (5, 2, 'Espagnol', 12),
            (6, 2, 'Allemand', 8),
            (7, 3, 'Piano', 18),
            (8, 3, 'Guitare', 22),
            (9, 4, 'Tennis', 10),
            (10, 4, 'Football', 14),
            (11, 5, 'Pâtisserie', 16),
            (12, 5, 'Cuisine italienne', 11),
            (13, 6, 'Mathématiques', 9),
            (14, 7, 'Dessin', 13)");

        // 3. Rates (seulement pour échanges et cours)
        $this->addSql("INSERT INTO rate (id, name, amount) VALUES 
            (1, 'Échange', 0),
            (2, 'Cours Standard', 25),
            (3, 'Cours Premium', 50)");

        // 4. Locations  
        $this->addSql("INSERT INTO location (id, adress, zip_code, city) VALUES 
            (1, '123 Rue de la République', '44000', 'Nantes'),
            (2, '456 Avenue des Champs-Élysées', '75008', 'Paris'),
            (3, '789 Cours Mirabeau', '13100', 'Aix-en-Provence'),
            (4, '321 Place Bellecour', '69002', 'Lyon'),
            (5, '654 Rue du Vieux-Port', '13001', 'Marseille')");

        // 5. Users (10 utilisateurs + 1 admin conservé)
        $adminPassword = '$2y$13$IbmRelIH/WiNgzmgIVgkyeZ2C.D0mT2sjZq.fWnrewmiPjrtcph9m'; // admin123
        $userPassword = '$2y$13$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'; // password
        
        $this->addSql("INSERT INTO `user` (id, email, roles, password, firstname, lastname, biography, avatar_path, phone, balance) VALUES
            (1, 'admin@learningshare.com', '[\"ROLE_ADMIN\"]', '$adminPassword', 'Admin', 'System', 'Administrateur de la plateforme LearningShare', 'avatar-default.png', '0666666666', 1000),
            (2, 'alice.martin@example.com', '[\"ROLE_USER\"]', '$userPassword', 'Alice', 'Martin', 'Passionnée de programmation et de musique', 'avatar-default.png', '0123456789', 150),
            (3, 'bob.dupont@example.com', '[\"ROLE_USER\"]', '$userPassword', 'Bob', 'Dupont', 'Chef cuisinier amateur, j\\'adore partager mes recettes', 'avatar-default.png', '0234567890', 200),
            (4, 'claire.bernard@example.com', '[\"ROLE_USER\"]', '$userPassword', 'Claire', 'Bernard', 'Professeure d\\'anglais freelance', 'avatar-default.png', '0345678901', 75),
            (5, 'david.rousseau@example.com', '[\"ROLE_USER\"]', '$userPassword', 'David', 'Rousseau', 'Musicien professionnel, piano et guitare', 'avatar-default.png', '0456789012', 120),
            (6, 'emma.leclerc@example.com', '[\"ROLE_USER\"]', '$userPassword', 'Emma', 'Leclerc', 'Développeuse web full-stack', 'avatar-default.png', '0567890123', 180),
            (7, 'francois.moreau@example.com', '[\"ROLE_USER\"]', '$userPassword', 'François', 'Moreau', 'Coach sportif spécialisé en tennis', 'avatar-default.png', '0678901234', 90),
            (8, 'gabrielle.lambert@example.com', '[\"ROLE_USER\"]', '$userPassword', 'Gabrielle', 'Lambert', 'Artiste peintre et professeure de dessin', 'avatar-default.png', '0789012345', 110),
            (9, 'henri.simon@example.com', '[\"ROLE_USER\"]', '$userPassword', 'Henri', 'Simon', 'Étudiant en informatique, passionné de Python', 'avatar-default.png', '0890123456', 50),
            (10, 'isabelle.garcia@example.com', '[\"ROLE_USER\"]', '$userPassword', 'Isabelle', 'Garcia', 'Polyglotte, parle 5 langues couramment', 'avatar-default.png', '0901234567', 160),
            (11, 'julien.petit@example.com', '[\"ROLE_USER\"]', '$userPassword', 'Julien', 'Petit', 'Mathématicien et pâtissier du dimanche', 'avatar-default.png', '0912345678', 130)");

        // 6. Lessons
        $this->addSql("INSERT INTO lesson (id, location_id, host_id, max_attendees) VALUES
            (1, 1, 2, 8),
            (2, 2, 4, 12),
            (3, 3, 5, 6),
            (4, 4, 6, 10),
            (5, 5, 7, 8),
            (6, 1, 8, 15),
            (7, 2, 10, 20),
            (8, 3, 11, 12),
            (9, 4, 3, 10),
            (10, 5, 9, 6)");

        // 7. Exchanges
        $this->addSql("INSERT INTO exchange (id, skill_requested_id, requester_id, attendee_id) VALUES
            (1, 1, 9, 2),
            (2, 4, 3, 4),
            (3, 7, 6, 5),
            (4, 9, 8, 7),
            (5, 11, 10, 3),
            (6, 2, 11, 6),
            (7, 8, 4, 5),
            (8, 13, 7, 11),
            (9, 14, 2, 8),
            (10, 5, 5, 10)");

        // 8. Sessions (mélange de lessons et exchanges)
        $this->addSql("INSERT INTO session (id, cost_id, skill_taught_id, exchange_id, lesson_id, start_time, end_time, date, description) VALUES
            (1, 2, 1, NULL, 1, '14:00:00', '16:00:00', '2025-06-05', 'Cours de PHP pour débutants'),
            (2, 3, 4, NULL, 2, '10:00:00', '12:00:00', '2025-06-06', 'Anglais conversationnel avancé'),
            (3, 2, 7, NULL, 3, '16:00:00', '18:00:00', '2025-06-07', 'Initiation au piano'),
            (4, 3, 2, NULL, 4, '09:00:00', '11:00:00', '2025-06-08', 'JavaScript moderne ES6+'),
            (5, 2, 9, NULL, 5, '15:00:00', '17:00:00', '2025-06-09', 'Tennis pour débutants'),
            (6, 1, 1, 1, NULL, '18:00:00', '20:00:00', '2025-06-10', 'Échange PHP contre Python'),
            (7, 1, 4, 2, NULL, '14:00:00', '15:30', '2025-06-11', 'Échange cuisine contre anglais'),
            (8, 1, 7, 3, NULL, '19:00:00', '21:00:00', '2025-06-12', 'Échange JavaScript contre piano'),
            (9, 1, 9, 4, NULL, '17:00:00', '18:30', '2025-06-13', 'Échange dessin contre tennis'),
            (10, 2, 14, NULL, 6, '11:00:00', '13:00:00', '2025-06-14', 'Atelier de dessin artistique'),
            (11, 3, 5, NULL, 7, '16:00:00', '18:00:00', '2025-06-15', 'Espagnol niveau intermédiaire'),
            (12, 2, 13, NULL, 8, '14:00:00', '16:00:00', '2025-06-16', 'Mathématiques appliquées'),
            (13, 2, 11, NULL, 9, '10:00:00', '12:00:00', '2025-06-17', 'Pâtisserie française'),
            (14, 1, 11, 5, NULL, '15:00:00', '16:30', '2025-06-18', 'Échange espagnol contre pâtisserie'),
            (15, 1, 2, 6, NULL, '20:00:00', '21:30', '2025-06-19', 'Échange maths contre JavaScript'),
            (16, 3, 3, NULL, 10, '13:00:00', '15:00:00', '2025-06-20', 'Python avancé et frameworks'),
            (17, 1, 8, 7, NULL, '18:30:00', '20:00:00', '2025-06-21', 'Échange anglais contre guitare'),
            (18, 1, 13, 8, NULL, '16:00:00', '17:30', '2025-06-22', 'Échange tennis contre mathématiques'),
            (19, 1, 14, 9, NULL, '19:00:00', '20:30', '2025-06-23', 'Échange PHP contre dessin'),
            (20, 1, 5, 10, NULL, '17:00:00', '18:30', '2025-06-24', 'Échange piano contre espagnol')");

        // 9. User_lesson (participants aux cours)
        $this->addSql("INSERT INTO user_lesson (user_id, lesson_id) VALUES 
            (3, 1), (4, 1), (5, 1),
            (6, 2), (7, 2), (8, 2), (9, 2),
            (2, 3), (6, 3), (10, 3),
            (3, 4), (5, 4), (7, 4), (9, 4),
            (2, 5), (4, 5), (6, 5),
            (3, 6), (4, 6), (5, 6), (7, 6),
            (2, 7), (3, 7), (6, 7), (8, 7), (9, 7),
            (2, 8), (4, 8), (6, 8), (10, 8),
            (4, 9), (5, 9), (7, 9), (8, 9),
            (2, 10), (3, 10), (4, 10)");

        // 10. Reviews (chaque utilisateur a entre 1 et 3 reviews)
        $this->addSql("INSERT INTO review (id, review_receiver_id, review_giver_id, content, rating) VALUES
            (1, 2, 3, 'Excellente explication du PHP, très pédagogue !', 5),
            (2, 2, 4, 'Alice maîtrise vraiment bien son sujet, je recommande vivement.', 5),
            (3, 2, 9, 'Super session, j\\'ai enfin compris les concepts avancés de PHP.', 4),
            (4, 3, 4, 'Bob cuisine divinement bien, ses recettes sont délicieuses !', 5),
            (5, 3, 10, 'Très bon échange, j\\'ai appris plein de techniques culinaires.', 4),
            (6, 4, 3, 'Claire est une excellente professeure d\\'anglais, très patiente.', 5),
            (7, 4, 6, 'Cours d\\'anglais très dynamique et intéressant !', 4),
            (8, 4, 2, 'Merci Claire pour cet excellent cours de conversation.', 5),
            (9, 5, 6, 'David joue magnifiquement bien, ses cours sont inspirants.', 5),
            (10, 5, 2, 'Session piano très enrichissante, David est un excellent pédagogue.', 4),
            (11, 5, 7, 'J\\'ai adoré l\\'échange guitare contre tennis, David est très talentueux.', 5),
            (12, 6, 3, 'Emma maîtrise parfaitement JavaScript, cours très professionnel.', 5),
            (13, 6, 5, 'Excellent échange, Emma explique très clairement les concepts.', 4),
            (14, 7, 8, 'François est un super coach de tennis, très motivant !', 5),
            (15, 7, 4, 'Cours de tennis très bien structuré, j\\'ai beaucoup progressé.', 4),
            (16, 8, 2, 'Gabrielle est une artiste remarquable et une excellente prof.', 5),
            (17, 8, 7, 'Atelier de dessin fantastique, Gabrielle a beaucoup de talent.', 5),
            (18, 8, 9, 'Très bon échange, j\\'ai appris de nouvelles techniques de dessin.', 4),
            (19, 9, 2, 'Henri connaît très bien Python, explications claires et précises.', 4),
            (20, 9, 6, 'Session Python très instructive, Henri est passionné par son sujet.', 4),
            (21, 10, 5, 'Isabelle parle un espagnol parfait, cours très enrichissant.', 5),
            (22, 10, 3, 'Excellent échange linguistique avec Isabelle, très pédagogue.', 5),
            (23, 10, 7, 'Isabelle m\\'a aidé à perfectionner mon espagnol, je recommande !', 4),
            (24, 11, 4, 'Julien explique les maths de façon très accessible.', 4),
            (25, 11, 6, 'Cours de mathématiques excellent, Julien est très patient.', 5),
            (26, 11, 8, 'Super échange maths contre pâtisserie, Julien est multi-talentueux !', 5)");

        // Réinitialiser les auto-increments
        $this->addSql('ALTER TABLE category AUTO_INCREMENT = 8');
        $this->addSql('ALTER TABLE skill AUTO_INCREMENT = 15');
        $this->addSql('ALTER TABLE rate AUTO_INCREMENT = 4');
        $this->addSql('ALTER TABLE location AUTO_INCREMENT = 6');
        $this->addSql('ALTER TABLE `user` AUTO_INCREMENT = 12');
        $this->addSql('ALTER TABLE lesson AUTO_INCREMENT = 11');
        $this->addSql('ALTER TABLE exchange AUTO_INCREMENT = 11');
        $this->addSql('ALTER TABLE session AUTO_INCREMENT = 21');
        $this->addSql('ALTER TABLE review AUTO_INCREMENT = 27');
    }

    public function down(Schema $schema): void
    {
        // Nettoyage complet
        $this->addSql('SET FOREIGN_KEY_CHECKS = 0');
        $this->addSql('TRUNCATE TABLE user_lesson');
        $this->addSql('TRUNCATE TABLE review');
        $this->addSql('TRUNCATE TABLE session');
        $this->addSql('TRUNCATE TABLE exchange');
        $this->addSql('TRUNCATE TABLE lesson');
        $this->addSql('TRUNCATE TABLE skill');
        $this->addSql('TRUNCATE TABLE `user`');
        $this->addSql('TRUNCATE TABLE location');
        $this->addSql('TRUNCATE TABLE rate');
        $this->addSql('TRUNCATE TABLE category');
        $this->addSql('SET FOREIGN_KEY_CHECKS = 1');
    }
}
