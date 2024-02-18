<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218172409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE credit (id INT AUTO_INCREMENT NOT NULL, credit_number VARCHAR(50) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, amount NUMERIC(30, 2) NOT NULL, credit_card_number VARCHAR(16) NOT NULL, credit_card_pin VARCHAR(4) NOT NULL, main_account_id INT NOT NULL, percent_account_id INT NOT NULL, client_id INT NOT NULL, credit_plan_id INT NOT NULL, INDEX IDX_1CC16EFEA3932BD9 (main_account_id), INDEX IDX_1CC16EFE6A3DCA68 (percent_account_id), INDEX IDX_1CC16EFE19EB6921 (client_id), INDEX IDX_1CC16EFED21D65B4 (credit_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE credit_plan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, month_period INT NOT NULL, percent DOUBLE PRECISION NOT NULL, min_amount NUMERIC(30, 2) NOT NULL, main_account_plan_id INT NOT NULL, percent_account_plan_id INT NOT NULL, INDEX IDX_DA98E440B735B216 (main_account_plan_id), INDEX IDX_DA98E4403D8FA982 (percent_account_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFEA3932BD9 FOREIGN KEY (main_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFE6A3DCA68 FOREIGN KEY (percent_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFED21D65B4 FOREIGN KEY (credit_plan_id) REFERENCES credit_plan (id)');
        $this->addSql('ALTER TABLE credit_plan ADD CONSTRAINT FK_DA98E440B735B216 FOREIGN KEY (main_account_plan_id) REFERENCES account_plan (id)');
        $this->addSql('ALTER TABLE credit_plan ADD CONSTRAINT FK_DA98E4403D8FA982 FOREIGN KEY (percent_account_plan_id) REFERENCES account_plan (id)');
        $this->addSql('ALTER TABLE system_info CHANGE system_datetime_info system_datetime_info DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY FK_1CC16EFEA3932BD9');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY FK_1CC16EFE6A3DCA68');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY FK_1CC16EFE19EB6921');
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY FK_1CC16EFED21D65B4');
        $this->addSql('ALTER TABLE credit_plan DROP FOREIGN KEY FK_DA98E440B735B216');
        $this->addSql('ALTER TABLE credit_plan DROP FOREIGN KEY FK_DA98E4403D8FA982');
        $this->addSql('DROP TABLE credit');
        $this->addSql('DROP TABLE credit_plan');
        $this->addSql('ALTER TABLE system_info CHANGE system_datetime_info system_datetime_info DATETIME DEFAULT NULL');
    }
}
