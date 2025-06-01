<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420092222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
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
            ALTER TABLE user_lesson ADD CONSTRAINT FK_9D266FCEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lesson ADD CONSTRAINT FK_9D266FCECDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role DROP FOREIGN KEY fk_userrole_role
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role DROP FOREIGN KEY fk_userrole_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE attend
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE app_user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_role
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blacklist MODIFY blacklist_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON blacklist
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blacklist CHANGE blacklist_id id INT AUTO_INCREMENT NOT NULL, CHANGE badword_array bad_words JSON DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blacklist ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category MODIFY category_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON category
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD name VARCHAR(255) NOT NULL, DROP category_name, CHANGE category_id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange ADD id INT AUTO_INCREMENT NOT NULL, ADD attendee_id INT DEFAULT NULL, DROP exchange_requester_id, DROP exchange_accepter_id, CHANGE skill_requested_id skill_requested_id INT NOT NULL, CHANGE exchange_session_id requester_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)
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
            CREATE INDEX IDX_D33BB079A5AB4B5 ON exchange (skill_requested_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D33BB079ED442CF4 ON exchange (requester_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D33BB079BCFD782A ON exchange (attendee_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD id INT AUTO_INCREMENT NOT NULL, DROP lesson_host_id, CHANGE lesson_session_id host_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD CONSTRAINT FK_F87474F364D218E FOREIGN KEY (location_id) REFERENCES location (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD CONSTRAINT FK_F87474F31FB8D185 FOREIGN KEY (host_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F87474F364D218E ON lesson (location_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F87474F31FB8D185 ON lesson (host_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location MODIFY location_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON location
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location CHANGE zip_code zip_code VARCHAR(255) NOT NULL, CHANGE city city VARCHAR(255) NOT NULL, CHANGE location_id id INT AUTO_INCREMENT NOT NULL, CHANGE address adress VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rate MODIFY rate_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON rate
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rate ADD name VARCHAR(255) NOT NULL, DROP rate_name, CHANGE rate_id id INT AUTO_INCREMENT NOT NULL, CHANGE rate_amount amount INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rate ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report MODIFY report_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON report
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report CHANGE report_giver_id report_giver_id INT NOT NULL, CHANGE report_receiver_id report_receiver_id INT NOT NULL, CHANGE report_id id INT AUTO_INCREMENT NOT NULL, CHANGE report_content content VARCHAR(500) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report ADD CONSTRAINT FK_C42F778475DD8454 FOREIGN KEY (report_giver_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report ADD CONSTRAINT FK_C42F77847C4B46BE FOREIGN KEY (report_receiver_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C42F778475DD8454 ON report (report_giver_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C42F77847C4B46BE ON report (report_receiver_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review MODIFY review_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON review
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD about_id INT NOT NULL, ADD content VARCHAR(500) NOT NULL, DROP review_content, DROP review_receiver_id, DROP review_session_id, CHANGE review_giver_id review_giver_id INT NOT NULL, CHANGE review_id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD CONSTRAINT FK_794381C6D087DB59 FOREIGN KEY (about_id) REFERENCES session (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD CONSTRAINT FK_794381C656FF9422 FOREIGN KEY (review_giver_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_794381C6D087DB59 ON review (about_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_794381C656FF9422 ON review (review_giver_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE role MODIFY role_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON role
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE role ADD name VARCHAR(255) NOT NULL, DROP role_name, CHANGE role_id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE role ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session MODIFY session_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON session
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD cost_id INT NOT NULL, ADD lesson_id INT DEFAULT NULL, ADD date DATE DEFAULT NULL, DROP date_session, CHANGE start_time start_time TIME DEFAULT NULL, CHANGE end_time end_time TIME DEFAULT NULL, CHANGE skill_taught_id skill_taught_id INT NOT NULL, CHANGE session_id id INT AUTO_INCREMENT NOT NULL, CHANGE rate_id exchange_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D41DBF857F FOREIGN KEY (cost_id) REFERENCES rate (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D47B48CA04 FOREIGN KEY (skill_taught_id) REFERENCES skill (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D468AFD1A0 FOREIGN KEY (exchange_id) REFERENCES exchange (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD CONSTRAINT FK_D044D5D4CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D044D5D41DBF857F ON session (cost_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D044D5D47B48CA04 ON session (skill_taught_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_D044D5D468AFD1A0 ON session (exchange_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_D044D5D4CDF80196 ON session (lesson_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill MODIFY skill_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill DROP FOREIGN KEY fk_skill_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `primary` ON skill
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill ADD name VARCHAR(255) NOT NULL, DROP skill_name, CHANGE category_id category_id INT NOT NULL, CHANGE search_counter search_counter INT NOT NULL, CHANGE skill_id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill ADD CONSTRAINT FK_5E3DE47712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill ADD PRIMARY KEY (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill RENAME INDEX fk_skill_category TO IDX_5E3DE47712469DE2
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB079ED442CF4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB079BCFD782A
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
            ALTER TABLE review DROP FOREIGN KEY FK_794381C656FF9422
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE attend (attend_id INT NOT NULL, attend_lesson_id INT DEFAULT NULL, attend_user_id INT DEFAULT NULL, PRIMARY KEY(attend_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE app_user (user_id INT AUTO_INCREMENT NOT NULL, mail VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, user_first_name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, user_last_name VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, biography VARCHAR(250) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, avatar_path VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_0900_ai_ci`, phone VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, balance INT DEFAULT 100, PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_role (role_id INT NOT NULL, user_id INT NOT NULL, INDEX fk_userrole_user (user_id), INDEX IDX_2DE8C6A3D60322AC (role_id), PRIMARY KEY(role_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role ADD CONSTRAINT fk_userrole_role FOREIGN KEY (role_id) REFERENCES role (role_id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role ADD CONSTRAINT fk_userrole_user FOREIGN KEY (user_id) REFERENCES app_user (user_id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lesson DROP FOREIGN KEY FK_9D266FCEA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_lesson DROP FOREIGN KEY FK_9D266FCECDF80196
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
        $this->addSql(<<<'SQL'
            ALTER TABLE location MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON location
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location CHANGE zip_code zip_code VARCHAR(50) NOT NULL, CHANGE city city VARCHAR(100) NOT NULL, CHANGE id location_id INT AUTO_INCREMENT NOT NULL, CHANGE adress address VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE location ADD PRIMARY KEY (location_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE role MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON role
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE role ADD role_name VARCHAR(50) NOT NULL, DROP name, CHANGE id role_id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE role ADD PRIMARY KEY (role_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange DROP FOREIGN KEY FK_D33BB079A5AB4B5
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D33BB079A5AB4B5 ON exchange
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D33BB079ED442CF4 ON exchange
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D33BB079BCFD782A ON exchange
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON exchange
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange ADD exchange_accepter_id INT DEFAULT NULL, DROP id, CHANGE skill_requested_id skill_requested_id INT DEFAULT NULL, CHANGE requester_id exchange_session_id INT NOT NULL, CHANGE attendee_id exchange_requester_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE exchange ADD PRIMARY KEY (exchange_session_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F364D218E
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F87474F364D218E ON lesson
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F87474F31FB8D185 ON lesson
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON lesson
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD lesson_host_id INT DEFAULT NULL, DROP id, CHANGE host_id lesson_session_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE lesson ADD PRIMARY KEY (lesson_session_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review DROP FOREIGN KEY FK_794381C6D087DB59
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_794381C6D087DB59 ON review
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_794381C656FF9422 ON review
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON review
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD review_content VARCHAR(500) DEFAULT NULL, ADD review_receiver_id INT DEFAULT NULL, ADD review_session_id INT DEFAULT NULL, DROP about_id, DROP content, CHANGE review_giver_id review_giver_id INT DEFAULT NULL, CHANGE id review_id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD PRIMARY KEY (review_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON category
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD category_name VARCHAR(50) NOT NULL, DROP name, CHANGE id category_id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD PRIMARY KEY (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blacklist MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON blacklist
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blacklist CHANGE id blacklist_id INT AUTO_INCREMENT NOT NULL, CHANGE bad_words badword_array JSON DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE blacklist ADD PRIMARY KEY (blacklist_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D41DBF857F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D47B48CA04
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D468AFD1A0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4CDF80196
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D044D5D41DBF857F ON session
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_D044D5D47B48CA04 ON session
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_D044D5D468AFD1A0 ON session
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_D044D5D4CDF80196 ON session
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON session
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD date_session DATE NOT NULL, ADD rate_id INT DEFAULT NULL, DROP cost_id, DROP exchange_id, DROP lesson_id, DROP date, CHANGE skill_taught_id skill_taught_id INT DEFAULT NULL, CHANGE start_time start_time TIME NOT NULL, CHANGE end_time end_time TIME NOT NULL, CHANGE id session_id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE session ADD PRIMARY KEY (session_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C42F778475DD8454 ON report
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_C42F77847C4B46BE ON report
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON report
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report CHANGE report_giver_id report_giver_id INT DEFAULT NULL, CHANGE report_receiver_id report_receiver_id INT DEFAULT NULL, CHANGE id report_id INT AUTO_INCREMENT NOT NULL, CHANGE content report_content VARCHAR(500) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE report ADD PRIMARY KEY (report_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill DROP FOREIGN KEY FK_5E3DE47712469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON skill
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill ADD skill_name VARCHAR(50) NOT NULL, DROP name, CHANGE category_id category_id INT DEFAULT NULL, CHANGE search_counter search_counter INT DEFAULT 0, CHANGE id skill_id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill ADD CONSTRAINT fk_skill_category FOREIGN KEY (category_id) REFERENCES category (category_id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill ADD PRIMARY KEY (skill_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE skill RENAME INDEX idx_5e3de47712469de2 TO fk_skill_category
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rate MODIFY id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX `PRIMARY` ON rate
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rate ADD rate_name VARCHAR(50) NOT NULL, DROP name, CHANGE id rate_id INT AUTO_INCREMENT NOT NULL, CHANGE amount rate_amount INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rate ADD PRIMARY KEY (rate_id)
        SQL);
    }
}
