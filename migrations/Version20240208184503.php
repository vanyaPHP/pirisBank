<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240208184503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, account_number VARCHAR(13) NOT NULL, debit_value NUMERIC(10, 2) NOT NULL, credit_value NUMERIC(10, 2) NOT NULL, balance NUMERIC(10, 2) NOT NULL, account_plan_id INT NOT NULL, INDEX IDX_7D3656A421B216D3 (account_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE account_plan (id INT AUTO_INCREMENT NOT NULL, account_number VARCHAR(4) NOT NULL, account_name VARCHAR(50) NOT NULL, account_type VARCHAR(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE deposit (id INT AUTO_INCREMENT NOT NULL, deposit_number VARCHAR(50) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, amount NUMERIC(10, 2) NOT NULL, deposit_plan_id INT NOT NULL, client_id INT NOT NULL, main_account_id INT NOT NULL, percent_account_id INT NOT NULL, INDEX IDX_95DB9D39E953F8C0 (deposit_plan_id), INDEX IDX_95DB9D3919EB6921 (client_id), INDEX IDX_95DB9D39A3932BD9 (main_account_id), INDEX IDX_95DB9D396A3DCA68 (percent_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE deposit_plan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, day_period INT NOT NULL, percent DOUBLE PRECISION NOT NULL, is_revocable TINYINT(1) NOT NULL, min_amount NUMERIC(10, 2) NOT NULL, main_account_plan_id INT NOT NULL, percent_account_plan_id INT NOT NULL, INDEX IDX_EE8E6C66B735B216 (main_account_plan_id), INDEX IDX_EE8E6C663D8FA982 (percent_account_plan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, transaction_day DATETIME NOT NULL, amount NUMERIC(10, 2) NOT NULL, debit_account_id INT NOT NULL, credit_account_id INT NOT NULL, INDEX IDX_723705D1204C4EAA (debit_account_id), INDEX IDX_723705D16813E404 (credit_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A421B216D3 FOREIGN KEY (account_plan_id) REFERENCES account_plan (id)');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D39E953F8C0 FOREIGN KEY (deposit_plan_id) REFERENCES deposit_plan (id)');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D3919EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D39A3932BD9 FOREIGN KEY (main_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE deposit ADD CONSTRAINT FK_95DB9D396A3DCA68 FOREIGN KEY (percent_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE deposit_plan ADD CONSTRAINT FK_EE8E6C66B735B216 FOREIGN KEY (main_account_plan_id) REFERENCES account_plan (id)');
        $this->addSql('ALTER TABLE deposit_plan ADD CONSTRAINT FK_EE8E6C663D8FA982 FOREIGN KEY (percent_account_plan_id) REFERENCES account_plan (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1204C4EAA FOREIGN KEY (debit_account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D16813E404 FOREIGN KEY (credit_account_id) REFERENCES account (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account DROP FOREIGN KEY FK_7D3656A421B216D3');
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D39E953F8C0');
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D3919EB6921');
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D39A3932BD9');
        $this->addSql('ALTER TABLE deposit DROP FOREIGN KEY FK_95DB9D396A3DCA68');
        $this->addSql('ALTER TABLE deposit_plan DROP FOREIGN KEY FK_EE8E6C66B735B216');
        $this->addSql('ALTER TABLE deposit_plan DROP FOREIGN KEY FK_EE8E6C663D8FA982');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1204C4EAA');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D16813E404');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE account_plan');
        $this->addSql('DROP TABLE deposit');
        $this->addSql('DROP TABLE deposit_plan');
        $this->addSql('DROP TABLE transaction');
    }
}
