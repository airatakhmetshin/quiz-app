<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240412120439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "answer_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "question_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "question_result_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "answer" (id INT NOT NULL, question_id INT NOT NULL, text VARCHAR(255) NOT NULL, is_correct BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DADD4A251E27F6BF ON "answer" (question_id)');
        $this->addSql('CREATE TABLE "question" (id INT NOT NULL, text VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "question_result" (id INT NOT NULL, session_id VARCHAR(255) NOT NULL, question_id INT NOT NULL, answer_ids JSON NOT NULL, status VARCHAR(10) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1437EE1B613FECDF1E27F6BF ON "question_result" (session_id, question_id)');
        $this->addSql('COMMENT ON COLUMN "question_result".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "answer" ADD CONSTRAINT FK_DADD4A251E27F6BF FOREIGN KEY (question_id) REFERENCES "question" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "answer_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "question_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE "question_result_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "answer" DROP CONSTRAINT FK_DADD4A251E27F6BF');
        $this->addSql('DROP TABLE "answer"');
        $this->addSql('DROP TABLE "question"');
        $this->addSql('DROP TABLE "question_result"');
    }
}
