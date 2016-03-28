<?php
namespace TYPO3\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20160328150800 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql');

        $this->addSql("UPDATE typo3_typo3cr_domain_model_nodedata SET nodetype='BJD.Events:Agenda' WHERE nodetype='Nieuwenhuizen.BuJitsuDo:Agenda'");
        $this->addSql("UPDATE typo3_typo3cr_domain_model_nodedata SET nodetype='BJD.Events:EventCalendar' WHERE nodetype='Nieuwenhuizen.BuJitsuDo:EventCalendar'");
        $this->addSql("UPDATE typo3_typo3cr_domain_model_nodedata SET nodetype='BJD.Events:Event' WHERE nodetype='Nieuwenhuizen.BuJitsuDo:Event'");
        $this->addSql("UPDATE typo3_typo3cr_domain_model_nodedata SET nodetype='BJD.Events:Exam' WHERE nodetype='Nieuwenhuizen.BuJitsuDo:Exam'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql');

        $this->addSql("UPDATE typo3_typo3cr_domain_model_nodedata SET nodetype='Nieuwenhuizen.BuJitsuDo:Agenda' WHERE nodetype='BJD.Events:Agenda'");
        $this->addSql("UPDATE typo3_typo3cr_domain_model_nodedata SET nodetype='Nieuwenhuizen.BuJitsuDo:EventCalendar' WHERE nodetype='BJD.Events:EventCalendar'");
        $this->addSql("UPDATE typo3_typo3cr_domain_model_nodedata SET nodetype='Nieuwenhuizen.BuJitsuDo:Event' WHERE nodetype='BJD.Events:Event'");
        $this->addSql("UPDATE typo3_typo3cr_domain_model_nodedata SET nodetype='Nieuwenhuizen.BuJitsuDo:Exam' WHERE nodetype='BJD.Events:Exam'");
    }
}
