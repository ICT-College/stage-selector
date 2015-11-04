<?= $this->Html->link(__('Back'), [
    'action' => 'index'
], [
    'class' => 'btn btn-default'
]) ?>
<br/><br/>
<table class="table">
    <tr>
        <th><?= h(__('Start')); ?></th>
        <td><?= h($period->start); ?></td>
    </tr>
    <tr>
        <th><?= h(__('End')); ?></th>
        <td><?= h($period->start); ?></td>
    </tr>
</table>

<table class="table">
    <thead>
        <tr>
            <th><?= h(__('Name')); ?></th>
            <th><?= h(__('Position')); ?></th>
        </tr>
    </thead>
    <?php foreach ($period->internships as $internship): ?>
        <tr>
            <td><?= h($internship->user->name); ?></td>
            <td>
                <?php if ($internship->position): ?>
                    <?= h($internship->position->study_program->description . ' - ' . $internship->position->company->name); ?>
                <?php else: ?>
                    <?= h(__('None')); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

