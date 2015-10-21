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
                <?= $this->Form->postLink(__('Remove'), ['action' => 'delete', $internshipApplication->id], ['class' => 'btn btn-danger']); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
