<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231225090500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE phone (id INT AUTO_INCREMENT NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE med_team ADD phone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE med_team ADD CONSTRAINT FK_3BC065143B7323CB FOREIGN KEY (phone_id) REFERENCES phone (id)');
        $this->addSql('CREATE INDEX IDX_3BC065143B7323CB ON med_team (phone_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE med_team DROP FOREIGN KEY FK_3BC065143B7323CB');
        $this->addSql('DROP TABLE phone');
        $this->addSql('DROP INDEX IDX_3BC065143B7323CB ON med_team');
        $this->addSql('ALTER TABLE med_team DROP phone_id');
    }
}
