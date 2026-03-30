<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240514090640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD operator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calling ADD CONSTRAINT FK_A606C573584598A3 FOREIGN KEY (operator_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A606C573584598A3 ON calling (operator_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP FOREIGN KEY FK_A606C573584598A3');
        $this->addSql('DROP INDEX IDX_A606C573584598A3 ON calling');
        $this->addSql('ALTER TABLE calling DROP operator_id');
    }
}
