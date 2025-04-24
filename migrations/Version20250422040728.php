<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422040728 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE functionality_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE menu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE profile_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE functionality (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, router VARCHAR(255) DEFAULT NULL, var_get VARCHAR(255) DEFAULT NULL, var_post VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE menu (id INT NOT NULL, id_profile_id INT DEFAULT NULL, id_functionality_id INT DEFAULT NULL, id_menu_parent INT DEFAULT NULL, orden VARCHAR(255) DEFAULT NULL, menu VARCHAR(255) DEFAULT NULL, language VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7D053A936970926F ON menu (id_profile_id)');
        $this->addSql('CREATE INDEX IDX_7D053A93E0148819 ON menu (id_functionality_id)');
        $this->addSql('CREATE TABLE profile (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A936970926F FOREIGN KEY (id_profile_id) REFERENCES profile (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A93E0148819 FOREIGN KEY (id_functionality_id) REFERENCES functionality (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE functionality_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE menu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE profile_id_seq CASCADE');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A936970926F');
        $this->addSql('ALTER TABLE menu DROP CONSTRAINT FK_7D053A93E0148819');
        $this->addSql('DROP TABLE functionality');
        $this->addSql('DROP TABLE menu');
        $this->addSql('DROP TABLE profile');
    }
}
