<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241227075448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM calling_rows WHERE calling_id IS NULL');
        $this->addSql('ALTER TABLE calling_rows DROP FOREIGN KEY FK_549F674C6F1895A1');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE calling_rows ADD CONSTRAINT FK_549F674C6F1895A1 FOREIGN KEY (calling_id) REFERENCES calling (id)');
    }
}
