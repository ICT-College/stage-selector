<div class="page-header">
    <h2><?= $internship->user->name; ?> <small><?= __('Internship selection'); ?></small></h2>
</div>

<div class="btn-group">
    <?= $this->Html->link(__('Back'), 'javascript: window.history.back();', [
        'class' => 'btn btn-danger'
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
            <th><?= h(__('Name')); ?></th>
            <td><?= h($internship->user->name); ?></td>
        </tr>
        <tr>
            <th><?= h(__('Position')); ?></th>
            <td>
                <?php if ($internship->position): ?>
                    <?= h($internship->position->study_program->description . ' - ' . $internship->position->company->name); ?>
                <?php else: ?>
                    <?= h(__('None')); ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><?= h(__('Period start')); ?></th>
            <td><?= h($internship->period->start); ?></td>
        </tr>
        <tr>
            <th><?= h(__('Period end')); ?></th>
            <td><?= h($internship->period->end); ?></td>
        </tr>
    </tbody>
</table>

<h2><?= h(__('Applications')); ?></h2>

<h3><?= h(__('Available')); ?></h3>
<table class="table">
    <thead>
    <tr>
        <th style="width: 20%;"><?= h(__('Date')); ?></th>
        <th style="width: 35%;"><?= h(__('Study program')); ?></th>
        <th style="width: 25%;"><?= h(__('Company name')); ?></th>
        <th style="width: 20%;">&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($internshipApplications as $internshipApplication): ?>
        <?php
        if ($internshipApplication->accepted_coordinator):
            continue;
        endif;
        ?>
        <tr>
            <td><?= h($internshipApplication->application_date->timeAgoInWords()); ?></td>
            <td>
                <?= $this->Html->link($internshipApplication->position->study_program->description, [
                    'plugin' => false,
                    'controller' => 'Positions',
                    'action' => 'view',
                    $internshipApplication->position->id
                ]); ?>
            </td>
            <td>
                <?= $this->Html->link($internshipApplication->position->company->name, [
                    'plugin' => false,
                    'controller' =>
                        'Companies',
                    'action' => 'view',
                    $internshipApplication->position->company->id
                ]); ?>
            </td>
            <td>
                <?= $this->Form->postLink(__('Approve'), [
                    'student_id' => $internshipApplication->student_id,
                    'action' => 'approve',
                    $internshipApplication->id
                ], [
                    'class' => 'btn btn-success'
                ]); ?>

                <?= $this->Form->postLink(__('Remove'), [
                    'student_id' => $internshipApplication->student_id,
                    'action' => 'delete',
                    $internshipApplication->id
                ], [
                    'class' => 'btn btn-danger'
                ]); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h3><?= h(__('Already approved')); ?></h3>
<table class="table">
    <thead>
    <tr>
        <th style="width: 20%;"><?= h(__('Date')); ?></th>
        <th style="width: 35%;"><?= h(__('Study program')); ?></th>
        <th style="width: 25%;"><?= h(__('Company name')); ?></th>
        <th style="width: 20%;">&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($internshipApplications as $internshipApplication): ?>
        <?php
        if (!$internshipApplication->accepted_coordinator):
            continue;
        endif;
        ?>
        <tr>
            <td><?= h($internshipApplication->application_date->timeAgoInWords()); ?></td>
            <td>
                <?= $this->Html->link($internshipApplication->position->study_program->description, [
                    'plugin' => false,
                    'controller' => 'Positions',
                    'action' => 'view',
                    $internshipApplication->position->id
                ]); ?>
            </td>
            <td>
                <?= $this->Html->link($internshipApplication->position->company->name, [
                    'plugin' => false,
                    'controller' => 'Companies',
                    'action' => 'view',
                    $internshipApplication->position->company->id
                ]); ?>
            </td>
            <td>
                <?= $this->Form->postLink(__('Remove'), [
                    'student_id' => $internshipApplication->student_id,
                    'action' => 'delete',
                    $internshipApplication->id
                ], [
                    'class' => 'btn btn-danger'
                ]); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
