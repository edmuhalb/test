<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612211902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP transport_charge_mileage, DROP transport_charge_toll_road, DROP transport_charge_parking_fees');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD transport_charge_mileage INT DEFAULT NULL, ADD transport_charge_toll_road INT DEFAULT NULL, ADD transport_charge_parking_fees INT DEFAULT NULL');
    }
}
