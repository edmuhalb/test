<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240616013416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calling_images (call_id INT NOT NULL, media_object_id INT NOT NULL, INDEX IDX_62C9AF2150A89B2C (call_id), INDEX IDX_62C9AF2164DE5A5 (media_object_id), PRIMARY KEY(call_id, media_object_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calling_images ADD CONSTRAINT FK_62C9AF2150A89B2C FOREIGN KEY (call_id) REFERENCES calling (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE calling_images ADD CONSTRAINT FK_62C9AF2164DE5A5 FOREIGN KEY (media_object_id) REFERENCES media_object (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calling_images DROP FOREIGN KEY FK_62C9AF2150A89B2C');
        $this->addSql('ALTER TABLE calling_images DROP FOREIGN KEY FK_62C9AF2164DE5A5');
        $this->addSql('DROP TABLE calling_images');
    }
}
