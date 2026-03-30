<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241208142424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD arrival_date_time DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD end_of_service_date_time DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP arrival_date_time, DROP end_of_service_date_time');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
