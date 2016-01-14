<div class="page-header">
    <h2><?= $student->name; ?> <small><?= __('Student information'); ?></small></h2>
</div>

<div class="btn-toolbar">
    <div class="btn-group">
        <?= $this->Html->link(__('Back'), 'javascript: window.history.back();', [
            'class' => 'btn btn-danger'
        ]); ?>
    </div>

    <div class="btn-group">
        <?= $this->Form->postLink(__('Invite'), [
            'controller' => 'Users',
            'action' => 'invite',
            $student->student_number
        ], [
            'class' => 'btn btn-default'
        ]); ?>
        <?= $this->Html->link(__('Edit'), 'javascript: window.history.back();', [
            'class' => 'btn btn-default'
        ]); ?>
        <?= $this->Html->link(__('Delete'), 'javascript: window.history.back();', [
            'class' => 'btn btn-danger'
        ]); ?>
    </div>

    <div class="btn-group">
        <?= $this->Html->link(__('View applications'), [
            'plugin' => 'IctCollege/CoordinatorApprovedSelector',
            'controller' => 'InternshipApplications',
            'action' => 'index',
            'student_id' => $student->student_id
        ], [
            'class' => 'btn btn-default'
        ]); ?>
    </div>
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
</table>

<table class="table">
    <caption><?= h(__('Internships')); ?></caption>
    <thead>
    <tr>
        <th><?= h(__('Period')); ?></th>
        <th><?= h(__('Position')); ?></th>
        <th><?= h(__('Accepted')); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($student->internships as $internship): ?>
        <tr>
            <td><?= h($internship->period->title); ?></td>
            <td><?= $this->Html->link($internship->position->study_program->description . ' - ' . $internship->position->company->name, ['controller' => 'Internships', 'action' => 'view', $internship->id]); ?></td>
            <td class="<?= ($internship->accepted) ? 'success' : 'danger' ?>">
                <?php if (!$internship->accepted): ?>
                    <em><?= h(__('Missing:')); ?></em>
                    <ul>
                        <?php if (!$internship->accepted_by_student): ?>
                            <li><?= h(__('Student')); ?></li>
                        <?php endif; ?>
                        <?php if (!$internship->accepted_by_coordinator): ?>
                            <li><?= h(__('Coordinator')); ?></li>
                        <?php endif; ?>
                        <?php if (!$internship->accepted_by_company): ?>
                            <li><?= h(__('Company')); ?></li>
                        <?php endif; ?>
                    </ul>

                <?php else: ?>
                    <?= h(__('Yes')); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
