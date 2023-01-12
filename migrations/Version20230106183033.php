<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230106183033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, profil_recruteur_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, allowed TINYINT(1) NOT NULL, salary VARCHAR(255) NOT NULL, hours INT NOT NULL, INDEX IDX_F65593E5CD8E2678 (profil_recruteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidature (id INT AUTO_INCREMENT NOT NULL, annonce_id INT NOT NULL, profil_candidat_id INT NOT NULL, allowed TINYINT(1) NOT NULL, INDEX IDX_E33BD3B88805AB2F (annonce_id), INDEX IDX_E33BD3B852EEF2C2 (profil_candidat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE create_account (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, role VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5F0EC1A3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil_candidat (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, cv_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_765CC1DCA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil_recruteur (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, society_name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_83A851ACA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5CD8E2678 FOREIGN KEY (profil_recruteur_id) REFERENCES profil_recruteur (id)');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B88805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id)');
        $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B852EEF2C2 FOREIGN KEY (profil_candidat_id) REFERENCES profil_candidat (id)');
        $this->addSql('ALTER TABLE create_account ADD CONSTRAINT FK_5F0EC1A3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE profil_candidat ADD CONSTRAINT FK_765CC1DCA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE profil_recruteur ADD CONSTRAINT FK_83A851ACA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5CD8E2678');
        $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B88805AB2F');
        $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B852EEF2C2');
        $this->addSql('ALTER TABLE create_account DROP FOREIGN KEY FK_5F0EC1A3A76ED395');
        $this->addSql('ALTER TABLE profil_candidat DROP FOREIGN KEY FK_765CC1DCA76ED395');
        $this->addSql('ALTER TABLE profil_recruteur DROP FOREIGN KEY FK_83A851ACA76ED395');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE candidature');
        $this->addSql('DROP TABLE create_account');
        $this->addSql('DROP TABLE profil_candidat');
        $this->addSql('DROP TABLE profil_recruteur');
        $this->addSql('DROP TABLE `user`');
    }
}
