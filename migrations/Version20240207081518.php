<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207081518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE citizenship (id INT AUTO_INCREMENT NOT NULL, citizenship_name VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, city_name VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(20) NOT NULL, middle_name VARCHAR(20) NOT NULL, last_name VARCHAR(20) NOT NULL, birth_date DATETIME NOT NULL, sex TINYINT(1) NOT NULL, passport_series VARCHAR(2) NOT NULL, passport_num VARCHAR(7) NOT NULL, passport_provider VARCHAR(30) NOT NULL, passport_release_date DATETIME NOT NULL, passport_id VARCHAR(14) NOT NULL, birth_place VARCHAR(50) NOT NULL, current_live_address VARCHAR(50) NOT NULL, home_phone VARCHAR(5) DEFAULT NULL, mobile_phone VARCHAR(13) DEFAULT NULL, email VARCHAR(40) DEFAULT NULL, registration_address VARCHAR(50) NOT NULL, is_pensioner TINYINT(1) NOT NULL, month_salary INT DEFAULT NULL, live_city_id INT NOT NULL, family_status_id INT NOT NULL, citizenship_id INT NOT NULL, disability_id INT NOT NULL, INDEX IDX_C7440455A7122D4 (live_city_id), INDEX IDX_C7440455A2399AD0 (family_status_id), INDEX IDX_C7440455C9709C85 (citizenship_id), INDEX IDX_C7440455709924E5 (disability_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE disability (id INT AUTO_INCREMENT NOT NULL, disability_name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE family_status (id INT AUTO_INCREMENT NOT NULL, family_status_name VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A7122D4 FOREIGN KEY (live_city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A2399AD0 FOREIGN KEY (family_status_id) REFERENCES family_status (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455C9709C85 FOREIGN KEY (citizenship_id) REFERENCES citizenship (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455709924E5 FOREIGN KEY (disability_id) REFERENCES disability (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A7122D4');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A2399AD0');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455C9709C85');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455709924E5');
        $this->addSql('DROP TABLE citizenship');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE disability');
        $this->addSql('DROP TABLE family_status');
    }
}
