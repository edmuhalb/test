<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214140516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE med_team ADD driver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE med_team ADD CONSTRAINT FK_3BC06514C3423909 FOREIGN KEY (driver_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3BC06514C3423909 ON med_team (driver_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE med_team DROP FOREIGN KEY FK_3BC06514C3423909');
        $this->addSql('DROP INDEX IDX_3BC06514C3423909 ON med_team');
        $this->addSql('ALTER TABLE med_team DROP driver_id');
    }
}
