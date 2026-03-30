<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240319161135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital_hospitals ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, DROP created_by, DROP updated_by');
        $this->addSql('ALTER TABLE hospital_hospitals ADD CONSTRAINT FK_2A085EBAB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE hospital_hospitals ADD CONSTRAINT FK_2A085EBA896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2A085EBAB03A8386 ON hospital_hospitals (created_by_id)');
        $this->addSql('CREATE INDEX IDX_2A085EBA896DBBDE ON hospital_hospitals (updated_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital_hospitals DROP FOREIGN KEY FK_2A085EBAB03A8386');
        $this->addSql('ALTER TABLE hospital_hospitals DROP FOREIGN KEY FK_2A085EBA896DBBDE');
        $this->addSql('DROP INDEX IDX_2A085EBAB03A8386 ON hospital_hospitals');
        $this->addSql('DROP INDEX IDX_2A085EBA896DBBDE ON hospital_hospitals');
        $this->addSql('ALTER TABLE hospital_hospitals ADD created_by VARCHAR(255) DEFAULT NULL, ADD updated_by VARCHAR(255) DEFAULT NULL, DROP created_by_id, DROP updated_by_id');
    }
}
