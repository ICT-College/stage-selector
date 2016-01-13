<div class="page-header">
    <h2><?= $student->name; ?> <small><?= __('Student information'); ?></small></h2>
</div>

<div class="btn-group">
    <?= $this->Html->link(__('Back'), 'javascript: window.history.back();', [
        'class' => 'btn btn-danger'
    ]); ?>&nbsp;
    <?= $this->Form->postLink(__('Invite'), [
        'controller' => 'Users',
        'action' => 'invite',
        $student->student_number
    ], [
        'class' => 'btn btn-default'
    ]); ?>
    <?= $this->Html->link(__('View applications'), [
        'plugin' => 'IctCollege/CoordinatorApprovedSelector',
        'controller' => 'InternshipApplications',
        'action' => 'index',
        'student_id' => $student->student_id
    ], [
        'class' => 'btn btn-default'
    ]); ?>
</div>

<table class="table">
    <thead>
        <tr>
            <th style="width: 20%;">&nbsp;</th>
            <th><?= __('Value'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th><?= __('Student number'); ?></th>
            <td><?= $student->student_number; ?></td>
        </tr>
        <tr>
            <th><?= __('Name'); ?></th>
            <td><?= $student->name; ?></td>
        </tr>
        <tr>
            <th><?= __('email'); ?></th>
            <td><?= $student->email; ?></td>
        </tr>
        <tr>
            <th><?= __('Group'); ?></th>
            <td><?= $student->groupcode; ?></td>
        </tr>
        <tr>
            <th><?= __('Learning pathway'); ?></th>
            <td><?= $student->learning_pathway; ?></td>
        </tr>
        <tr>
            <th><?= __('Program number'); ?></th>
            <td><?= $student->study_program_id; ?></td>
        </tr>
    </tbody>
</table
