<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230410015210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD price INT DEFAULT NULL, ADD estimated INT DEFAULT NULL, ADD prepayment INT DEFAULT NULL, ADD note VARCHAR(255) DEFAULT NULL, ADD passport LONGTEXT DEFAULT NULL, ADD coast_hospital INT DEFAULT NULL, ADD cost_day INT DEFAULT NULL, ADD phone_relatives VARCHAR(255) DEFAULT NULL, ADD result_date VARCHAR(255) DEFAULT NULL, ADD result_time VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP price, DROP estimated, DROP prepayment, DROP note, DROP passport, DROP coast_hospital, DROP cost_day, DROP phone_relatives, DROP result_date, DROP result_time');
    }
}
