<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250414162321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Categories
        $this->addSql("INSERT INTO category (id, name) VALUES (1, 'Music'), (2, 'Programming'), (3, 'Cooking')");

        // Roles
        $this->addSql("INSERT INTO role (id, name) VALUES (1, 'Admin'), (2, 'User')");

        // Rates
        $this->addSql("INSERT INTO rate (id, name, amount) VALUES (1, 'Free', 0), (2, 'Standard', 10)");

        // Locations
        $this->addSql("INSERT INTO location (id, adress, zip_code, city) VALUES 
            (1, '123 Main St', '44000', 'Nantes'),
            (2, '456 Side St', '75000', 'Paris')");

        // Pour l'admin, on utilise un mot de passe déjà haché qui correspond à 'admin123'
        // Ce hash a été généré avec l'algorithme auto pour Symfony 6.4/7.0
        $hashedPassword = '$2y$13$IbmRelIH/WiNgzmgIVgkyeZ2C.D0mT2sjZq.fWnrewmiPjrtcph9m';

        // Users
        $this->addSql("INSERT INTO `user` (id, email, roles, password, firstname, lastname, biography, avatar_path, phone, balance) VALUES
            (1, 'alice@example.com', '[]', 'pass1', 'Alice', 'Dupont', 'I love teaching music.', 'default.png', '0102030405', 100),
            (2, 'bob@example.com', '[]', 'pass2', 'Bob', 'Martin', 'Cooking is my passion.', 'default.png', '0607080910', 150),
            (3, 'charlie@example.com', '[]', 'pass3', 'Charlie', 'Durand', NULL, 'default.png', '0708091011', 50),
            (4, 'admin@example.com', '[\"ROLE_ADMIN\"]', '" . $hashedPassword . "', 'Admin', 'Admin', 'Administrateur du site', 'default.png', '0666666666', 1000)");



        // Skills
        $this->addSql("INSERT INTO skill (id, category_id, name, search_counter) VALUES
            (1, 1, 'Guitar', 5),
            (2, 2, 'PHP', 10),
            (3, 3, 'Baking', 3)");

        // Lessons
        $this->addSql("INSERT INTO lesson (id, location_id, host_id, max_attendees) VALUES
            (1, 1, 1, 5),
            (2, 2, 2, 8)");

        // UserLesson (pivot)
        $this->addSql("INSERT INTO user_lesson (user_id, lesson_id) VALUES 
            (2, 1),
            (3, 1),
            (1, 2)");

        // Exchanges
        $this->addSql("INSERT INTO exchange (id, skill_requested_id, requester_id, attendee_id) VALUES
            (1, 2, 1, 2),
            (2, 3, 3, NULL)");

        // Sessions
        $this->addSql("INSERT INTO session (id, cost_id, skill_taught_id, exchange_id, lesson_id, start_time, end_time, date, description) VALUES
            (1, 1, 1, 1, 1, '10:00:00', '12:00:00', '2025-12-20', 'Guitar session for beginners'),
            (2, 2, 2, NULL, 2, '14:00:00', '16:00:00', '2025-12-22', 'Advanced PHP workshop')");

        // Reviews
        $this->addSql("INSERT INTO review (id, about_id, review_giver_id, content, rating) VALUES
            (1, 1, 2, 'Great session!', 5),
            (2, 2, 3, 'Very informative.', 4)");

        // Reports
        $this->addSql("INSERT INTO report (id, report_giver_id, report_receiver_id, content) VALUES
            (1, 1, 2, 'Inappropriate behavior'),
            (2, 3, 1, 'No show for the session')");

        // Blacklist
        $this->addSql("INSERT INTO blacklist (id, bad_words) VALUES 
            (1, '[\"badword1\", \"badword2\"]')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM user_lesson");
        $this->addSql("DELETE FROM review");
        $this->addSql("DELETE FROM report");
        $this->addSql("DELETE FROM session");
        $this->addSql("DELETE FROM exchange");
        $this->addSql("DELETE FROM lesson");
        $this->addSql("DELETE FROM skill");
        $this->addSql("DELETE FROM `user`");
        $this->addSql("DELETE FROM location");
        $this->addSql("DELETE FROM rate");
        $this->addSql("DELETE FROM role");
        $this->addSql("DELETE FROM category");
        $this->addSql("DELETE FROM blacklist");
    }
}

