<div class="btn-group">
    <?= $this->Html->link(__('Go to selector'), ['controller' => 'Positions', 'action' => 'select'], ['class' => 'btn btn-primary']); ?>
</div>
<table class="table">
    <thead>
    <tr>
        <th><?= h(__('Date')); ?></th>
        <th><?= h(__('Study program')); ?></th>
        <th><?= h(__('Company')); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($internshipApplications as $internshipApplication): ?>
        <tr>
            <td><?= h($internshipApplication->application_date->timeAgoInWords()); ?></td>
            <td><?= h($internshipApplication->position->study_program->description); ?></td>
            <td>
                <address>
                    <strong><?= h($internshipApplication->position->company->name); ?></strong><br>
                    <?= h($internshipApplication->position->company->address); ?><br>
                    <?= h($internshipApplication->position->company->postcode); ?>, <?= h($internshipApplication->position->company->city); ?><br>
                    <abbr title="Phone">P</abbr> <?= h($internshipApplication->position->company->telephone); ?><br>
                    <abbr title="Email">E:</abbr> <?= $this->Html->link($internshipApplication->position->company->email, 'mailto:' . $internshipApplication->position->company->email1); ?>
                </address>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
