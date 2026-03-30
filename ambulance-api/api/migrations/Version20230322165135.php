<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230322165135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP FOREIGN KEY FK_A606C573296CD8AE');
        $this->addSql('DROP INDEX IDX_A606C573296CD8AE ON calling');
        $this->addSql('ALTER TABLE calling DROP team_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD team_id INT NOT NULL');
        $this->addSql('ALTER TABLE calling ADD CONSTRAINT FK_A606C573296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_A606C573296CD8AE ON calling (team_id)');
    }
}
