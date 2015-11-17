<table class="table">
    <tr>
        <th><th><?= h(__('Name')); ?></th></th>
        <td><?= h($internship->user->name); ?></td>
    </tr>
    <tr>
        <th><th><?= h(__('Position')); ?></th></th>
        <td>
            <?php if ($internship->position): ?>
                <?= h($internship->position->study_program->description . ' - ' . $internship->position->company->name); ?>
            <?php else: ?>
                <?= h(__('None')); ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th><th><?= h(__('Period start')); ?></th></th>
        <td><?= h($internship->period->start); ?></td>
    </tr>
    <tr>
        <th><th><?= h(__('Period end')); ?></th></th>
        <td><?= h($internship->period->end); ?></td>
    </tr>
</table>

<h1><?= h(__('Applications')); ?></h1>

<h2><?= h(__('Available')); ?></h2>
<table class="table">
    <thead>
    <tr>
        <th><?= h(__('Date')); ?></th>
        <th><?= h(__('Study program')); ?></th>
        <th><?= h(__('Company name')); ?></th>
        <th><?= h(__('Position description')); ?></th>
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
            <td><?= h($internshipApplication->position->study_program->description); ?></td>
            <td><?= $this->Html->link($internshipApplication->position->company->name, ['plugin' => false, 'controller' => 'Companies', 'action' => 'view', $internshipApplication->position->company->id]); ?></td>
            <td>
                <?php if ($internshipApplication->position->description): ?>
                    <?= h($internshipApplication->position->description); ?>
                <?php else: ?>
                    <em><?= h(__('None')); ?></em>
                <?php endif; ?>
            </td>
            <td>
                <?= $this->Form->postLink(__('Approve'), ['student_id' => $internshipApplication->student_id, 'action' => 'approve', $internshipApplication->id], ['class' => 'btn btn-success']); ?>
                <?= $this->Form->postLink(__('Remove'), ['student_id' => $internshipApplication->student_id, 'action' => 'delete', $internshipApplication->id], ['class' => 'btn btn-danger']); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h2><?= h(__('Already approved')); ?></h2>
<table class="table">
    <thead>
    <tr>
        <th><?= h(__('Date')); ?></th>
        <th><?= h(__('Study program')); ?></th>
        <th><?= h(__('Company name')); ?></th>
        <th><?= h(__('Position description')); ?></th>
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
            <td><?= h($internshipApplication->position->study_program->description); ?></td>
            <td><?= $this->Html->link($internshipApplication->position->company->name, ['plugin' => false, 'controller' => 'Companies', 'action' => 'view', $internshipApplication->position->company->id]); ?></td>
            <td>
                <?php if ($internshipApplication->position->description): ?>
                    <?= h($internshipApplication->position->description); ?>
                <?php else: ?>
                    <em><?= h(__('None')); ?></em>
                <?php endif; ?>
            </td>
            <td>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
