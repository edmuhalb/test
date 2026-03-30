<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240627092035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hospital_images (hospital_id INT NOT NULL, media_object_id INT NOT NULL, INDEX IDX_3F7E0B3963DBB69 (hospital_id), INDEX IDX_3F7E0B3964DE5A5 (media_object_id), PRIMARY KEY(hospital_id, media_object_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hospital_images ADD CONSTRAINT FK_3F7E0B3963DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital_hospitals (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE hospital_images ADD CONSTRAINT FK_3F7E0B3964DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital_images DROP FOREIGN KEY FK_3F7E0B3963DBB69');
        $this->addSql('ALTER TABLE hospital_images DROP FOREIGN KEY FK_3F7E0B3964DE5A5');
        $this->addSql('DROP TABLE hospital_images');
    }
}
