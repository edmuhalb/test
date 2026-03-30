<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240315091504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car ADD is_caddy TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE med_team ADD planned_duty_start_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD planned_duty_finish_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE car DROP is_caddy');
        $this->addSql('ALTER TABLE med_team DROP planned_duty_start_at, DROP planned_duty_finish_at');
    }
}
