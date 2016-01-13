<div class="page-header">
    <h2><?= $position->study_program->description; ?> <small><?= __('Position information'); ?></small></h2>
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
            <th><?= __('Company'); ?></th>
            <td>
                <?= $this->Html->link($position->company->name, [
                    'controller' => 'Companies',
                    'action' => 'view',
                    $position->company_id
                ]); ?>
            </td>
        </tr>
        <tr>
            <th><?= __('Study program'); ?></th>
            <td><?= $position->study_program->description; ?> (<?= $position->study_program->id; ?>)</td>
        </tr>
        <tr>
            <th><?= __('Learning pathway'); ?></th>
            <td><?= ($position->learning_pathway == 'GV') ? __('BBL/BOL') : $position->learning_pathway; ?></td>
        </tr>
        <tr>
            <th><?= __('Description'); ?></th>
            <td>
                <pre><?= ((empty($position->description)) ? __('No description') : h($position->description)); ?></pre>
            </td>
        </tr>
        <tr>
            <th><?= __('Available between'); ?></th>
            <td><?= $position->start; ?> - <?= $position->end; ?></td>
        </tr>
    </tbody>
</table>

<h3><?= __('Qualification parts') ?></h3>
<table class="table">
    <thead>
        <tr>
            <th style="width: 5%;">&nbsp;</th>
            <th><?= __('Name'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($position->qualification_parts as $qualification_part): ?>
            <tr>
                <td><?= $qualification_part->number; ?></td>
                <td><?= $qualification_part->description; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
