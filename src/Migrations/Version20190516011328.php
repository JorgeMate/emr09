<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190516011328 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE patient ADD code1 VARCHAR(63) DEFAULT NULL, ADD code2 VARCHAR(63) DEFAULT NULL, CHANGE insurance_id insurance_id INT DEFAULT NULL, CHANGE source_id source_id INT DEFAULT NULL, CHANGE firstname firstname VARCHAR(127) DEFAULT NULL, CHANGE sex sex TINYINT(1) DEFAULT NULL, CHANGE date_of_birth date_of_birth DATE DEFAULT NULL, CHANGE email email VARCHAR(180) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE house_no house_no SMALLINT DEFAULT NULL, CHANGE house_txt house_txt VARCHAR(15) DEFAULT NULL, CHANGE city city VARCHAR(63) DEFAULT NULL, CHANGE postcode postcode VARCHAR(31) DEFAULT NULL, CHANGE tel tel VARCHAR(31) DEFAULT NULL, CHANGE cel cel VARCHAR(31) DEFAULT NULL, CHANGE contact contact VARCHAR(127) DEFAULT NULL, CHANGE tel_con tel_con VARCHAR(31) DEFAULT NULL, CHANGE doctor doctor VARCHAR(127) DEFAULT NULL, CHANGE tel_doc tel_doc VARCHAR(31) DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE firstname firstname VARCHAR(127) DEFAULT NULL, CHANGE tel tel VARCHAR(63) DEFAULT NULL, CHANGE date_of_birth date_of_birth DATE DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE medic medic TINYINT(1) DEFAULT NULL, CHANGE cod cod VARCHAR(3) DEFAULT NULL, CHANGE title title VARCHAR(15) DEFAULT NULL, CHANGE speciality speciality VARCHAR(127) DEFAULT NULL, CHANGE internal internal TINYINT(1) DEFAULT NULL, CHANGE tax_code tax_code VARCHAR(63) DEFAULT NULL, CHANGE reg_code1 reg_code1 VARCHAR(63) DEFAULT NULL, CHANGE reg_code2 reg_code2 VARCHAR(63) DEFAULT NULL');
        $this->addSql('ALTER TABLE center_doc_group CHANGE center_id center_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE insurance CHANGE center_id center_id INT DEFAULT NULL, CHANGE code code VARCHAR(10) DEFAULT NULL, CHANGE contact contact VARCHAR(127) DEFAULT NULL, CHANGE email email VARCHAR(180) DEFAULT NULL, CHANGE tel tel VARCHAR(31) DEFAULT NULL, CHANGE fax fax VARCHAR(31) DEFAULT NULL');
        $this->addSql('ALTER TABLE source CHANGE center_id center_id INT DEFAULT NULL, CHANGE code code INT DEFAULT NULL, CHANGE contact contact VARCHAR(127) DEFAULT NULL, CHANGE email email VARCHAR(180) DEFAULT NULL, CHANGE tel tel VARCHAR(31) DEFAULT NULL, CHANGE fax fax VARCHAR(31) DEFAULT NULL');
        $this->addSql('ALTER TABLE place CHANGE contact contact VARCHAR(127) DEFAULT NULL, CHANGE email email VARCHAR(180) DEFAULT NULL, CHANGE tel tel VARCHAR(31) DEFAULT NULL, CHANGE fax fax VARCHAR(31) DEFAULT NULL');
        $this->addSql('ALTER TABLE center CHANGE user_id user_id INT DEFAULT NULL, CHANGE contact_person contact_person VARCHAR(255) DEFAULT NULL, CHANGE tel tel VARCHAR(63) DEFAULT NULL, CHANGE address address VARCHAR(127) DEFAULT NULL, CHANGE postcode postcode VARCHAR(31) DEFAULT NULL, CHANGE city city VARCHAR(63) DEFAULT NULL, CHANGE ssaas_account_name ssaas_account_name VARCHAR(127) DEFAULT NULL, CHANGE ssaas_api_key ssaas_api_key VARCHAR(127) DEFAULT NULL');
        $this->addSql('ALTER TABLE medicat CHANGE dosis dosis VARCHAR(127) DEFAULT NULL, CHANGE stop_at stop_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE opera CHANGE place_id place_id INT DEFAULT NULL, CHANGE note_id note_id INT DEFAULT NULL, CHANGE made_at made_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARCHAR(128) NOT NULL');
        $this->addSql('ALTER TABLE treatment CHANGE value value DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE stored_img CHANGE mime_type mime_type VARCHAR(127) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_doc CHANGE center_doc_group_id center_doc_group_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE mime_type mime_type VARCHAR(127) DEFAULT NULL');
        $this->addSql('ALTER TABLE user_log CHANGE logout_time logout_time DATETIME DEFAULT NULL, CHANGE logout_type logout_type SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE historia CHANGE date date DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE tag CHANGE center_id center_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE note CHANGE paid_at paid_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE consult CHANGE treatment treatment VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE center CHANGE user_id user_id INT DEFAULT NULL, CHANGE contact_person contact_person VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel tel VARCHAR(63) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE address address VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE postcode postcode VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE city city VARCHAR(63) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE ssaas_account_name ssaas_account_name VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE ssaas_api_key ssaas_api_key VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE center_doc_group CHANGE center_id center_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE consult CHANGE treatment treatment VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE historia CHANGE date date DATE DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE insurance CHANGE center_id center_id INT DEFAULT NULL, CHANGE code code VARCHAR(10) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE contact contact VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel tel VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE fax fax VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE medicat CHANGE dosis dosis VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE stop_at stop_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE note CHANGE paid_at paid_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE opera CHANGE place_id place_id INT DEFAULT NULL, CHANGE note_id note_id INT DEFAULT NULL, CHANGE made_at made_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE patient DROP code1, DROP code2, CHANGE insurance_id insurance_id INT DEFAULT NULL, CHANGE source_id source_id INT DEFAULT NULL, CHANGE firstname firstname VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE sex sex TINYINT(1) DEFAULT \'NULL\', CHANGE date_of_birth date_of_birth DATE DEFAULT \'NULL\', CHANGE email email VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE house_no house_no SMALLINT DEFAULT NULL, CHANGE house_txt house_txt VARCHAR(15) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE city city VARCHAR(63) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE postcode postcode VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel tel VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE cel cel VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE contact contact VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel_con tel_con VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE doctor doctor VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel_doc tel_doc VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE place CHANGE contact contact VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel tel VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE fax fax VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE sessions CHANGE sess_id sess_id VARCHAR(128) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE source CHANGE center_id center_id INT DEFAULT NULL, CHANGE code code INT DEFAULT NULL, CHANGE contact contact VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE email email VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel tel VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE fax fax VARCHAR(31) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE stored_img CHANGE mime_type mime_type VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE tag CHANGE center_id center_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE treatment CHANGE value value DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE firstname firstname VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE tel tel VARCHAR(63) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE date_of_birth date_of_birth DATE DEFAULT \'NULL\', CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE medic medic TINYINT(1) DEFAULT \'NULL\', CHANGE cod cod VARCHAR(3) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE title title VARCHAR(15) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE speciality speciality VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE internal internal TINYINT(1) DEFAULT \'NULL\', CHANGE tax_code tax_code VARCHAR(63) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE reg_code1 reg_code1 VARCHAR(63) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE reg_code2 reg_code2 VARCHAR(63) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user_doc CHANGE center_doc_group_id center_doc_group_id INT DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE mime_type mime_type VARCHAR(127) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE user_log CHANGE logout_time logout_time DATETIME DEFAULT \'NULL\', CHANGE logout_type logout_type SMALLINT DEFAULT NULL');
    }
}
