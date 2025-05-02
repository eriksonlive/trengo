<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250430184042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE SEQUENCE functionality_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        // $this->addSql('CREATE SEQUENCE menu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        // $this->addSql('CREATE SEQUENCE profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE functionality (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, router VARCHAR(255) DEFAULT NULL, var_get VARCHAR(255) DEFAULT NULL, var_post VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE menu (id INT NOT NULL, profile_id INT DEFAULT NULL, functionality_id INT DEFAULT NULL, id_menu_parent INT DEFAULT NULL, orden VARCHAR(255) DEFAULT NULL, menu VARCHAR(255) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D053A93CCFA12B8 ON menu (profile_id)');
        $this->addSql('CREATE INDEX IDX_7D053A9339EDDC8 ON menu (functionality_id)');
        $this->addSql('CREATE INDEX IDX_7D053A93F6DBF560 ON menu (id_menu_parent)');
        $this->addSql('CREATE TABLE profile (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A9339EDDC8 FOREIGN KEY (functionality_id) REFERENCES functionality (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93F6DBF560 FOREIGN KEY (id_menu_parent) REFERENCES menu (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tickets ALTER status SET NOT NULL');
        $this->addSql('ALTER TABLE tickets ALTER contact SET NOT NULL');
        $this->addSql('ALTER TABLE tickets ALTER created_at SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE functionality_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE menu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE profile_id_seq CASCADE');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A93CCFA12B8');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A9339EDDC8');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A93F6DBF560');
        $this->addSql('DROP TABLE functionality');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE profile');
        $this->addSql('ALTER TABLE tickets ALTER status DROP NOT NULL');
        $this->addSql('ALTER TABLE tickets ALTER contact DROP NOT NULL');
        $this->addSql('ALTER TABLE tickets ALTER created_at DROP NOT NULL');
    }
}
