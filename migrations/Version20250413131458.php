<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413131458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Insertion du tarif par défaut pour les sessions
        // Ici, nous créons un tarif "Standard" qui permettra d'insérer les sessions.
        $this->addSql(<<<'SQL'
            INSERT INTO rate (id, name, amount) VALUES (1, 'Standard', 0);
        SQL);

        // Insertion des catégories de compétences
        // Sélectionner la base de données n'est pas nécessaire en migration.
        $this->addSql(<<<'SQL'
            INSERT INTO category (id, name) VALUES
                (1, 'Programmation'),
                (2, 'Musique'),
                (3, 'Mathématiques'),
                (4, 'Design graphique'),
                (5, 'Photographie'),
                (6, 'Langues étrangères'),
                (7, 'Marketing digital'),
                (8, 'Rédaction'),
                (9, 'Gestion de projet'),
                (10, 'Cuisine');
        SQL);

        // Insertion des utilisateurs
        // MDP des users : "pass"
        // On insère un rôle par défaut pour chaque utilisateur.
        $this->addSql(<<<'SQL'
            INSERT INTO `user` (id, email, roles, password, firstname, lastname, biography, phone, balance, avatar_path) VALUES
                (1, 'alice@example.com', '["ROLE_USER"]', '$2y$10$PzrLrAzoR9garXQzFTyoxuYxHTNbUP3PKIHv2N6Oc4Cu85ZXqatZu', 'Alice', 'Dupont', 'Développeuse Python passionnée.', '0101010101', 100, '/assets/avatars/avatar-default.png'),
                (2, 'bob@example.com', '["ROLE_USER"]', '$2y$10$PzrLrAzoR9garXQzFTyoxuYxHTNbUP3PKIHv2N6Oc4Cu85ZXqatZu', 'Bob', 'Martin', 'Musicien, expert guitare et piano.', '0202020202', 100, '/assets/avatars/avatar-default.png'),
                (3, 'charlie@example.com', '["ROLE_USER"]', '$2y$10$PzrLrAzoR9garXQzFTyoxuYxHTNbUP3PKIHv2N6Oc4Cu85ZXqatZu', 'Charlie', 'Durand', 'Professeur de mathématiques, spécialiste de l algèbre et calcul.', '0303030303', 100, '/assets/avatars/avatar-default.png'),
                (4, 'david@example.com', '["ROLE_USER"]', '$2y$10$PzrLrAzoR9garXQzFTyoxuYxHTNbUP3PKIHv2N6Oc4Cu85ZXqatZu', 'David', 'Lemoine', 'Étudiant en gestion de projet.', '0404040404', 100, '/assets/avatars/avatar-default.png'),
                (5, 'eva@example.com', '["ROLE_USER"]', '$2y$10$PzrLrAzoR9garXQzFTyoxuYxHTNbUP3PKIHv2N6Oc4Cu85ZXqatZu', 'Eva', 'Petit', 'Photographe amateur, passionnée par la cuisine.', '0505050505', 100, '/assets/avatars/avatar-default.png'),
                (6, 'francois@example.com', '["ROLE_USER"]', '$2y$10$PzrLrAzoR9garXQzFTyoxuYxHTNbUP3PKIHv2N6Oc4Cu85ZXqatZu', 'François', 'Lemoine', 'Apprenant de l espagnol et gestion de projet.', '0606060606', 100, '/assets/avatars/avatar-default.png');
        SQL);

        // Insertion des compétences
        $this->addSql(<<<'SQL'
            INSERT INTO skill (id, category_id, name, search_counter) VALUES
                (1, 1, 'Python', 0),
                (2, 1, 'JavaScript', 0),
                (3, 2, 'Guitare', 0),
                (4, 2, 'Piano', 0),
                (5, 3, 'Calcul différentiel', 0),
                (6, 3, 'Algèbre', 0),
                (7, 4, 'Photoshop', 0),
                (8, 5, 'Photographie numérique', 0),
                (9, 6, 'Espagnol', 0),
                (10, 9, 'Gestion de projet agile', 0);
        SQL);

        // Insertion des lieux (locations)
        // Note: le champ utilisé est "adress" et non "address"
        $this->addSql(<<<'SQL'
            INSERT INTO location (id, adress, zip_code, city) VALUES
                (1, '10 Rue de Paris', '44100', 'Nantes'), -- Lieu 1
                (2, 'Cité des Congrès - 5 Rue de Valmy', '44000', 'Nantes'), -- Lieu 2
                (3, '50 Rue Julien Douillard', '44400', 'Rezé'), -- Lieu 3
                (4, '1 Rue Floréal', '44300', 'Nantes'); -- Lieu 4
        SQL);

        // Insertion des sessions
        // Utilisation de cost_id fixé à 1 (tarif Standard)
        $this->addSql(<<<'SQL'
            INSERT INTO session (id, cost_id, skill_taught_id, start_time, end_time, date, description) VALUES
                (1, 1, 1, '09:00:00', '12:00:00', '2025-05-01', 'Cours de Python pour débutants'), -- Python (session 1)
                (2, 1, 3, '10:00:00', '12:00:00', '2025-05-02', 'Échange de compétences sur la guitare'), -- Guitare (session 2)
                (3, 1, 6, '14:00:00', '16:00:00', '2025-05-03', 'Echange dalgèbre'), -- Algèbre (session 3)
                (4, 1, 4, '09:00:00', '11:00:00', '2025-05-04', 'Échange de compétences sur le piano'), -- Piano (session 4)
                (5, 1, 8, '13:00:00', '15:00:00', '2025-05-05', 'Cours de photographie numérique'), -- Photographie numérique (session 5)
                (6, 1, 10, '09:00:00', '12:00:00', '2025-05-06', 'Cours sur la gestion de projet agile'); -- Gestion de projet agile (session 6)
        SQL);

        // Lier les sessions avec les leçons
        // Dans le schéma Symfony, la table lesson ne contient pas de référence à une session.
        // On insère donc les informations relatives à l'emplacement, l'hôte et le nombre maximum d'inscrits.
        $this->addSql(<<<'SQL'
            INSERT INTO lesson (id, location_id, host_id, max_attendees) VALUES
                (1, 4, 1, 5), -- Cours Python (Session 1)
                (2, 1, 5, 10), -- Cours Photographie numérique (Session 5)
                (3, 2, 6, 10); -- Cours Gestion de projet agile (Session 6)
        SQL);

        // Lier les sessions avec les échanges
        // Le champ de session est supprimé dans le schéma, donc on insère uniquement les compétences et utilisateurs concernés.
        $this->addSql(<<<'SQL'
            INSERT INTO exchange (id, skill_requested_id, requester_id, attendee_id) VALUES
                (1, 6, 2, 4), -- Échange de compétences Guitare (Session 2)
                (2, 2, 3, NULL), -- Échange de compétences Algèbre (Session 3)
                (3, 9, 2, 6); -- Échange de compétences Piano (Session 4)
        SQL);

        // Participation des utilisateurs aux leçons et échanges
        // Pour lier la participation aux leçons, on utilise la table user_lesson (relation many-to-many)
        // Session 1 (Cours Python)
        $this->addSql(<<<'SQL'
            INSERT INTO user_lesson (user_id, lesson_id) VALUES
                (4, 1) -- Utilisateur 4 participe à Cours Python (Session 1)
        SQL);

        // Session 5 (Cours Photographie numérique)
        $this->addSql(<<<'SQL'
            INSERT INTO user_lesson (user_id, lesson_id) VALUES
                (6, 2) -- Utilisateur 6 participe à Cours Photographie numérique (Session 5)
        SQL);

        // Session 6 (Cours Gestion de projet agile)
        $this->addSql(<<<'SQL'
            INSERT INTO user_lesson (user_id, lesson_id) VALUES
                (5, 3) -- Utilisateur 5 participe à Cours Gestion de projet agile (Session 6)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Suppression des données insérées en cas de rollback
        $this->addSql('DELETE FROM user_lesson;');
        $this->addSql('DELETE FROM exchange;');
        $this->addSql('DELETE FROM lesson;');
        $this->addSql('DELETE FROM session;');
        $this->addSql('DELETE FROM location;');
        $this->addSql('DELETE FROM skill;');
        $this->addSql('DELETE FROM `user`;');
        $this->addSql('DELETE FROM category;');
        $this->addSql('DELETE FROM rate;');
    }
}
