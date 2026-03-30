<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240612132904 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD no_business_cards TINYINT(1) DEFAULT 0 NOT NULL, ADD partner_hospitalization TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE partner ADD no_business_cards TINYINT(1) DEFAULT 0 NOT NULL, ADD partner_hospitalization TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP no_business_cards, DROP partner_hospitalization');
        $this->addSql('ALTER TABLE partner DROP no_business_cards, DROP partner_hospitalization');
    }
}
