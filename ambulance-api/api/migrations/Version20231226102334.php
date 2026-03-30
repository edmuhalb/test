<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231226102334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_services ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service_services ADD CONSTRAINT FK_8A44BA5412469DE2 FOREIGN KEY (category_id) REFERENCES service_categories (id)');
        $this->addSql('CREATE INDEX IDX_8A44BA5412469DE2 ON service_services (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_services DROP FOREIGN KEY FK_8A44BA5412469DE2');
        $this->addSql('DROP INDEX IDX_8A44BA5412469DE2 ON service_services');
        $this->addSql('ALTER TABLE service_services DROP category_id');
    }
}
