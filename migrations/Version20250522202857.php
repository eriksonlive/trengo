<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250522202857 extends AbstractMigration
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
        $this->addSql('CREATE SEQUENCE ticket_email_message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        // $this->addSql('CREATE SEQUENCE ticket_messages_attachments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ticket_email_message (id INT NOT NULL, message_id BIGINT DEFAULT NULL, html TEXT DEFAULT NULL, email_to VARCHAR(255) DEFAULT NULL, email_from VARCHAR(255) DEFAULT NULL, emailcc VARCHAR(255) DEFAULT NULL, email_subject VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4AB9F42C537A1329 ON ticket_email_message (message_id)');
        $this->addSql('COMMENT ON COLUMN ticket_email_message.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN ticket_email_message.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE ticket_email_message ADD CONSTRAINT FK_4AB9F42C537A1329 FOREIGN KEY (message_id) REFERENCES ticket_messages (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE functionality_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE menu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE profile_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ticket_email_message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ticket_messages_attachments_id_seq CASCADE');
        $this->addSql('ALTER TABLE ticket_email_message DROP CONSTRAINT FK_4AB9F42C537A1329');
        $this->addSql('DROP TABLE ticket_email_message');
    }
}
