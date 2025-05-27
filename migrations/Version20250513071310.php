<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513071310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE review DROP FOREIGN KEY FK_794381C6D087DB59
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_794381C6D087DB59 ON review
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review CHANGE about_id review_receiver_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD CONSTRAINT FK_794381C6B39501CA FOREIGN KEY (review_receiver_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_794381C6B39501CA ON review (review_receiver_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE review DROP FOREIGN KEY FK_794381C6B39501CA
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_794381C6B39501CA ON review
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review CHANGE review_receiver_id about_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review ADD CONSTRAINT FK_794381C6D087DB59 FOREIGN KEY (about_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_794381C6D087DB59 ON review (about_id)
        SQL);
    }
}
