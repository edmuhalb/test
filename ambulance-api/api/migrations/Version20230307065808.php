<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230307065808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling ADD team_id INT NOT NULL, ADD name VARCHAR(128) NOT NULL, ADD phone VARCHAR(16) NOT NULL, ADD status VARCHAR(16) NOT NULL COMMENT \'(DC2Type:calling_status)\', ADD description LONGTEXT NOT NULL, ADD rejected_comment VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD accepted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD completed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE calling ADD CONSTRAINT FK_A606C573296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('CREATE INDEX IDX_A606C573296CD8AE ON calling (team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling DROP FOREIGN KEY FK_A606C573296CD8AE');
        $this->addSql('DROP INDEX IDX_A606C573296CD8AE ON calling');
        $this->addSql('ALTER TABLE calling DROP team_id, DROP name, DROP phone, DROP status, DROP description, DROP rejected_comment, DROP created_at, DROP accepted_at, DROP completed_at');
    }
}
