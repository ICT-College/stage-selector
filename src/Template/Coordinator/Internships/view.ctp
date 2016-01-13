<div class="page-header">
    <h2><?= $internship->user->name; ?> <small><?= __('Internship information'); ?></small></h2>
</div>

<div class="btn-group">
    <?= $this->Html->link(__('Back'), 'javascript: window.history.back();', [
        'class' => 'btn btn-danger'
    ]) ?>
</div>

<h3><?= h(__('Period')); ?></h3>

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
            <td><?= h($internship->period->start); ?></td>
        </tr>
        <tr>
            <th><?= h(__('End')); ?></th>
            <td><?= h($internship->period->end); ?></td>
        </tr>
    </tbody>
</table>

<h3><?= h(__('Student')); ?></h3>

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
    </tbody>
</table>

<h3><?= h(__('Position')); ?></h3>

<table class="table">
    <thead>
        <tr>
            <th style="width: 20%;">&nbsp;</th>
            <th><?= __('Value'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th><?= h(__('Description')); ?></th>
            <td><?= h($internship->position->study_program->description); ?></td>
        </tr>
        <tr>
            <th><?= h(__('Company')); ?></th>
            <td><?= h($internship->position->company->name); ?></td>
        </tr>
    </tbody>
</table>

