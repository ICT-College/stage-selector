<?php

use Phinx\Migration\AbstractMigration;

class InternshipAccept extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('internships')
            ->addColumn('accepted_by_student', 'boolean', [
                'null' => true,
                'after' => 'status'
            ])
            ->addColumn('accepted_by_student_date', 'timestamp', [
                'null' => true,
                'after' => 'accepted_by_student'
            ])
            ->addColumn('accepted_by_coordinator', 'boolean', [
                'null' => true,
                'after' => 'accepted_by_student_date'
            ])
            ->addColumn('accepted_by_coordinator_date', 'timestamp', [
                'null' => true,
                'after' => 'accepted_by_coordinator'
            ])
            ->addColumn('accepted_by_company', 'boolean', [
                'null' => true,
                'after' => 'accepted_by_coordinator_date'
            ])
            ->addColumn('accepted_by_company_date', 'timestamp', [
                'null' => true,
                'after' => 'accepted_by_company'
            ])
            ->addColumn('accepted', 'boolean', [
                'default' => false,
                'after' => 'accepted_by_company_date'
            ])
            ->update();
    }
}
