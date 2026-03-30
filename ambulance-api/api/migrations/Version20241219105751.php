<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219105751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling_rows DROP in_cash');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling_rows ADD in_cash TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
