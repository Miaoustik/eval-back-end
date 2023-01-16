<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230112194044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE annonce_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE candidature_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE create_account_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE profil_candidat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE profil_recruteur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE annonce (id INT NOT NULL, profil_recruteur_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, allowed BOOLEAN NOT NULL, salary VARCHAR(255) NOT NULL, hours INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F65593E5CD8E2678 ON annonce (profil_recruteur_id)');
        $this->addSql('CREATE TABLE candidature (id INT NOT NULL, annonce_id INT NOT NULL, profil_candidat_id INT NOT NULL, allowed BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E33BD3B88805AB2F ON candidature (annonce_id)');
        $this->addSql('CREATE INDEX IDX_E33BD3B852EEF2C2 ON candidature (profil_candidat_id)');
        $this->addSql('CREATE TABLE create_account (id INT NOT NULL, user_id INT NOT NULL, role VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5F0EC1A3A76ED395 ON create_account (user_id)');
        $this->addSql('CREATE TABLE profil_candidat (id INT NOT NULL, user_id INT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, cv_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_765CC1DCA76ED395 ON profil_candidat (user_id)');
        $this->addSql('CREATE TABLE profil_recruteur (id INT NOT NULL, user_id INT NOT NULL, society_name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_83A851ACA76ED395 ON profil_recruteur (user_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5CD8E2678 FOREIGN KEY (profil_recruteur_id) REFERENCES profil_recruteur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B88805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B852EEF2C2 FOREIGN KEY (profil_candidat_id) REFERENCES profil_candidat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE create_account ADD CONSTRAINT FK_5F0EC1A3A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE profil_candidat ADD CONSTRAINT FK_765CC1DCA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE profil_recruteur ADD CONSTRAINT FK_83A851ACA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE annonce_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE candidature_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE create_account_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE profil_candidat_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE profil_recruteur_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE annonce DROP CONSTRAINT FK_F65593E5CD8E2678');
        $this->addSql('ALTER TABLE candidature DROP CONSTRAINT FK_E33BD3B88805AB2F');
        $this->addSql('ALTER TABLE candidature DROP CONSTRAINT FK_E33BD3B852EEF2C2');
        $this->addSql('ALTER TABLE create_account DROP CONSTRAINT FK_5F0EC1A3A76ED395');
        $this->addSql('ALTER TABLE profil_candidat DROP CONSTRAINT FK_765CC1DCA76ED395');
        $this->addSql('ALTER TABLE profil_recruteur DROP CONSTRAINT FK_83A851ACA76ED395');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE candidature');
        $this->addSql('DROP TABLE create_account');
        $this->addSql('DROP TABLE profil_candidat');
        $this->addSql('DROP TABLE profil_recruteur');
        $this->addSql('DROP TABLE "user"');
    }
}
