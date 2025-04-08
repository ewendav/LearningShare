<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408092730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE blacklist (id INT AUTO_INCREMENT NOT NULL, bad_words JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE exchange (id INT AUTO_INCREMENT NOT NULL, skill_requested_id INT NOT NULL, requester_id INT NOT NULL, attendee_id INT DEFAULT NULL, INDEX IDX_D33BB079A5AB4B5 (skill_requested_id), INDEX IDX_D33BB079ED442CF4 (requester_id), INDEX IDX_D33BB079BCFD782A (attendee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE lesson (id INT AUTO_INCREMENT NOT NULL, location_id INT DEFAULT NULL, host_id INT NOT NULL, max_attendees INT NOT NULL, INDEX IDX_F87474F364D218E (location_id), INDEX IDX_F87474F31FB8D185 (host_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE location (id INT AUTO_INCREMENT NOT NULL, adress VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rate (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, amount INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, report_giver_id INT NOT NULL, report_receiver_id INT NOT NULL, content VARCHAR(500) NOT NULL, INDEX IDX_C42F778475DD8454 (report_giver_id), INDEX IDX_C42F77847C4B46BE (report_receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, about_id INT NOT NULL, review_giver_id INT NOT NULL, content VARCHAR(500) NOT NULL, rating INT NOT NULL, INDEX IDX_794381C6D087DB59 (about_id), INDEX IDX_794381C656FF9422 (review_giver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, cost_id INT NOT NULL, skill_taught_id INT NOT NULL, start_time TIME DEFAULT NULL, end_time TIME DEFAULT NULL, date DATE DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_D044D5D41DBF857F (cost_id), INDEX IDX_D044D5D47B48CA04 (skill_taught_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE skill (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, search_counter INT NOT NULL, INDEX IDX_5E3DE47712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, biography VARCHAR(500) DEFAULT NULL, avatar_path VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) NOT NULL, balance INT NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_lesson (user_id INT NOT NULL, lesson_id INT NOT NULL, INDEX IDX_9D266FCEA76ED395 (user_id), INDEX IDX_9D266FCECDF80196 (lesson_id), PRIMARY KEY(user_id, lesson_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange ADD CONSTRAINT FK_D33BB079A5AB4B5 FOREIGN KEY (skill_requested_id) REFERENCES skill (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange ADD CONSTRAINT FK_D33BB079ED442CF4 FOREIGN KEY (requester_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange ADD CONSTRAINT FK_D33BB079BCFD782A FOREIGN KEY (attendee_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD CONSTRAINT FK_F87474F364D218E FOREIGN KEY (location_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD CONSTRAINT FK_F87474F31FB8D185 FOREIGN KEY (host_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report ADD CONSTRAINT FK_C42F778475DD8454 FOREIGN KEY (report_giver_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report ADD CONSTRAINT FK_C42F77847C4B46BE FOREIGN KEY (report_receiver_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD CONSTRAINT FK_794381C6D087DB59 FOREIGN KEY (about_id) REFERENCES session (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD CONSTRAINT FK_794381C656FF9422 FOREIGN KEY (review_giver_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D41DBF857F FOREIGN KEY (cost_id) REFERENCES rate (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D47B48CA04 FOREIGN KEY (skill_taught_id) REFERENCES skill (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill ADD CONSTRAINT FK_5E3DE47712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lesson ADD CONSTRAINT FK_9D266FCEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lesson ADD CONSTRAINT FK_9D266FCECDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB079A5AB4B5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB079ED442CF4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB079BCFD782A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F364D218E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F31FB8D185
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report DROP FOREIGN KEY FK_C42F778475DD8454
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report DROP FOREIGN KEY FK_C42F77847C4B46BE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review DROP FOREIGN KEY FK_794381C6D087DB59
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review DROP FOREIGN KEY FK_794381C656FF9422
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D41DBF857F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D47B48CA04
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill DROP FOREIGN KEY FK_5E3DE47712469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lesson DROP FOREIGN KEY FK_9D266FCEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lesson DROP FOREIGN KEY FK_9D266FCECDF80196
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE blacklist
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE exchange
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE lesson
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE location
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rate
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE report
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE review
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE role
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE session
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE skill
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `user`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_lesson
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
