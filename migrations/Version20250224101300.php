<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224101300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blacklist (id SERIAL NOT NULL, bad_words JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE exchange (id SERIAL NOT NULL, skill_requested_id INT NOT NULL, requester_id INT NOT NULL, attendee_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D33BB079A5AB4B5 ON exchange (skill_requested_id)');
        $this->addSql('CREATE INDEX IDX_D33BB079ED442CF4 ON exchange (requester_id)');
        $this->addSql('CREATE INDEX IDX_D33BB079BCFD782A ON exchange (attendee_id)');
        $this->addSql('CREATE TABLE lesson (id SERIAL NOT NULL, location_id INT DEFAULT NULL, host_id INT NOT NULL, max_attendees INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F87474F364D218E ON lesson (location_id)');
        $this->addSql('CREATE INDEX IDX_F87474F31FB8D185 ON lesson (host_id)');
        $this->addSql('CREATE TABLE location (id SERIAL NOT NULL, adress VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE rate (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, amount INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE report (id SERIAL NOT NULL, report_giver_id INT NOT NULL, report_receiver_id INT NOT NULL, content VARCHAR(500) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C42F778475DD8454 ON report (report_giver_id)');
        $this->addSql('CREATE INDEX IDX_C42F77847C4B46BE ON report (report_receiver_id)');
        $this->addSql('CREATE TABLE review (id SERIAL NOT NULL, about_id INT NOT NULL, review_giver_id INT NOT NULL, content VARCHAR(500) NOT NULL, rating INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_794381C6D087DB59 ON review (about_id)');
        $this->addSql('CREATE INDEX IDX_794381C656FF9422 ON review (review_giver_id)');
        $this->addSql('CREATE TABLE role (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE session (id SERIAL NOT NULL, cost_id INT NOT NULL, skill_taught_id INT NOT NULL, start_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL, end_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL, date DATE DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D044D5D41DBF857F ON session (cost_id)');
        $this->addSql('CREATE INDEX IDX_D044D5D47B48CA04 ON session (skill_taught_id)');
        $this->addSql('CREATE TABLE skill (id SERIAL NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, search_counter INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5E3DE47712469DE2 ON skill (category_id)');
        $this->addSql('CREATE TABLE user_lesson (user_id INT NOT NULL, lesson_id INT NOT NULL, PRIMARY KEY(user_id, lesson_id))');
        $this->addSql('CREATE INDEX IDX_9D266FCEA76ED395 ON user_lesson (user_id)');
        $this->addSql('CREATE INDEX IDX_9D266FCECDF80196 ON user_lesson (lesson_id)');
        $this->addSql('ALTER TABLE exchange ADD CONSTRAINT FK_D33BB079A5AB4B5 FOREIGN KEY (skill_requested_id) REFERENCES skill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exchange ADD CONSTRAINT FK_D33BB079ED442CF4 FOREIGN KEY (requester_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE exchange ADD CONSTRAINT FK_D33BB079BCFD782A FOREIGN KEY (attendee_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F364D218E FOREIGN KEY (location_id) REFERENCES location (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F31FB8D185 FOREIGN KEY (host_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F778475DD8454 FOREIGN KEY (report_giver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77847C4B46BE FOREIGN KEY (report_receiver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6D087DB59 FOREIGN KEY (about_id) REFERENCES session (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C656FF9422 FOREIGN KEY (review_giver_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D41DBF857F FOREIGN KEY (cost_id) REFERENCES rate (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D47B48CA04 FOREIGN KEY (skill_taught_id) REFERENCES skill (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE skill ADD CONSTRAINT FK_5E3DE47712469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_lesson ADD CONSTRAINT FK_9D266FCEA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_lesson ADD CONSTRAINT FK_9D266FCECDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD firstname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD lastname VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD biography VARCHAR(500) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD avatar_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD balance INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE exchange DROP CONSTRAINT FK_D33BB079A5AB4B5');
        $this->addSql('ALTER TABLE exchange DROP CONSTRAINT FK_D33BB079ED442CF4');
        $this->addSql('ALTER TABLE exchange DROP CONSTRAINT FK_D33BB079BCFD782A');
        $this->addSql('ALTER TABLE lesson DROP CONSTRAINT FK_F87474F364D218E');
        $this->addSql('ALTER TABLE lesson DROP CONSTRAINT FK_F87474F31FB8D185');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F778475DD8454');
        $this->addSql('ALTER TABLE report DROP CONSTRAINT FK_C42F77847C4B46BE');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C6D087DB59');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C656FF9422');
        $this->addSql('ALTER TABLE session DROP CONSTRAINT FK_D044D5D41DBF857F');
        $this->addSql('ALTER TABLE session DROP CONSTRAINT FK_D044D5D47B48CA04');
        $this->addSql('ALTER TABLE skill DROP CONSTRAINT FK_5E3DE47712469DE2');
        $this->addSql('ALTER TABLE user_lesson DROP CONSTRAINT FK_9D266FCEA76ED395');
        $this->addSql('ALTER TABLE user_lesson DROP CONSTRAINT FK_9D266FCECDF80196');
        $this->addSql('DROP TABLE blacklist');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE exchange');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE location');
        $this->addSql('DROP TABLE rate');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE skill');
        $this->addSql('DROP TABLE user_lesson');
        $this->addSql('ALTER TABLE "user" DROP firstname');
        $this->addSql('ALTER TABLE "user" DROP lastname');
        $this->addSql('ALTER TABLE "user" DROP biography');
        $this->addSql('ALTER TABLE "user" DROP avatar_path');
        $this->addSql('ALTER TABLE "user" DROP phone');
        $this->addSql('ALTER TABLE "user" DROP balance');
    }
}
