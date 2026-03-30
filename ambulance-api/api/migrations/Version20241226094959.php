<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241226094959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calling_rows_files (calling_row_id INT NOT NULL, file_object_id INT NOT NULL, INDEX IDX_5113BA1117478B9 (calling_row_id), INDEX IDX_5113BA11AD22E95D (file_object_id), PRIMARY KEY(calling_row_id, file_object_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calling_rows_files ADD CONSTRAINT FK_5113BA1117478B9 FOREIGN KEY (calling_row_id) REFERENCES calling_rows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE calling_rows_files ADD CONSTRAINT FK_5113BA11AD22E95D FOREIGN KEY (file_object_id) REFERENCES file_object (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling_rows_files DROP FOREIGN KEY FK_5113BA1117478B9');
        $this->addSql('ALTER TABLE calling_rows_files DROP FOREIGN KEY FK_5113BA11AD22E95D');
        $this->addSql('DROP TABLE calling_rows_files');
        $this->addSql('ALTER TABLE work_schedule CHANGE city_id city_id INT DEFAULT 1');
    }
}
