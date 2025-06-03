<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250522050341 extends AbstractMigration
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
        // $this->addSql('CREATE SEQUENCE ticket_messages_attachments_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ticket_messages (id BIGINT NOT NULL, ticket_id BIGINT DEFAULT NULL, body_type VARCHAR(255) DEFAULT NULL, message TEXT DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, file_caption VARCHAR(255) DEFAULT NULL, contact VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, is_phone BOOLEAN DEFAULT NULL, agent_name VARCHAR(255) DEFAULT NULL, agent_email VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5E6BE217700047D2 ON ticket_messages (ticket_id)');
        $this->addSql('COMMENT ON COLUMN ticket_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN ticket_messages.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE ticket_messages_attachments (id INT NOT NULL, message_id BIGINT NOT NULL, client_name VARCHAR(255) DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, full_url VARCHAR(255) DEFAULT NULL, is_image BOOLEAN DEFAULT NULL, extencion VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8D37335E537A1329 ON ticket_messages_attachments (message_id)');
        $this->addSql('CREATE TABLE tickets (id BIGINT NOT NULL, status VARCHAR(255) NOT NULL, detenido_en VARCHAR(255) DEFAULT NULL, label_name VARCHAR(255) DEFAULT NULL, id_label INT DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, contact VARCHAR(255) NOT NULL, category VARCHAR(255) DEFAULT NULL, priority VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, closed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tickets.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tickets.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN tickets.closed_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE ticket_messages ADD CONSTRAINT FK_5E6BE217700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_messages_attachments ADD CONSTRAINT FK_8D37335E537A1329 FOREIGN KEY (message_id) REFERENCES ticket_messages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        // $this->addSql('DROP SEQUENCE functionality_id_seq CASCADE');
        // $this->addSql('DROP SEQUENCE menu_id_seq CASCADE');
        // $this->addSql('DROP SEQUENCE profile_id_seq CASCADE');
        // $this->addSql('DROP SEQUENCE ticket_messages_attachments_id_seq CASCADE');
        $this->addSql('ALTER TABLE ticket_messages DROP CONSTRAINT FK_5E6BE217700047D2');
        $this->addSql('ALTER TABLE ticket_messages_attachments DROP CONSTRAINT FK_8D37335E537A1329');
        $this->addSql('DROP TABLE ticket_messages');
        $this->addSql('DROP TABLE ticket_messages_attachments');
        $this->addSql('DROP TABLE tickets');
    }
}
