<?php
use Migrations\AbstractMigration;

class InternshipInterview extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $this->table('internships')
            ->addColumn('planned_interview_date', 'datetime', [
                'comment' => 'The planned date of the interview',
                'after' => 'report',
                'null' => true
            ])
            ->addColumn('contact_email', 'string', [
                'comment' => 'The email address used to contact the company',
                'after' => 'planned_interview_date',
                'null' => true
            ])
            ->update();
    }
}
