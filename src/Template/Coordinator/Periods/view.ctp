<div class="page-header">
    <h2><?= $period->start; ?> - <?= $period->end; ?> <small><?= __('Period information'); ?></small></h2>
</div>

<div class="btn-group">
    <?= $this->Html->link(__('Back'), 'javascript: window.history.back();', [
        'class' => 'btn btn-danger'
    ]) ?>
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
            <th><?= h(__('Start')); ?></th>
            <td><?= h($period->start); ?></td>
        </tr>
        <tr>
            <th><?= h(__('End')); ?></th>
            <td><?= h($period->end); ?></td>
        </tr>
    </tbody>
</table>

<table class="table">
    <thead>
        <tr>
            <th style="width: 20%;"><?= h(__('Name')); ?></th>
            <th><?= h(__('Position')); ?></th>
        </tr>
    </thead>
    <?php foreach ($period->internships as $internship): ?>
        <tr>
            <td>
                <?php if ($internship->user): ?>
                    <?= $this->Html->link(
                        $internship->user->name,
                        ['controller' => 'internships', 'action' => 'view', $internship->id]
                    ); ?>
                <?php else: ?>
                    <?= __('Unknown'); ?>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($internship->position): ?>
                    <?= $this->Html->link(
                        $internship->position->study_program->description,
                        ['controller' => 'Positions', 'action' => 'view', $internship->position->id]
                    ); ?> -
                    <?= $this->Html->link(
                        $internship->position->company->name,
                        ['controller' => 'Companies', 'action' => 'view', $internship->position->company->id]
                    ); ?>
                <?php else: ?>
                    <?= h(__('None')); ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

