<?= $this->start('search') ?>
    <?= $this->element('search') ?>
<?= $this->end() ?>
<div class="text-center"><?= $this->Paginator->numbers() ?></div>

<table class="table table-hover">
    <thead>
    <tr>
        <th><?= h(__('ID')) ?></th>
        <th><?= h(__('Start')) ?></th>
        <th><?= h(__('End')) ?></th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($periods as $period): ?>
        <tr>
            <td>
                <?= $this->Html->link($period->id, [
                    'action' => 'view',
                    $period->id
                ]) ?>
            </td>
            <td><?= $period->start ?></td>
            <td><?= $period->end ?></td>
            <td>
                <div class="btn-group">
                    <?= $this->Html->link(__('Edit'), [
                        'action' => 'edit',
                        $period->id
                    ], [
                        'class' => 'btn btn-default'
                    ]) ?>
                    <?= $this->Html->link(__('Delete'), [
                        'action' => 'delete',
                        $period->id
                    ], [
                        'confirm' => __('Are you sure?'),
                        'class' => 'btn btn-danger'
                    ]) ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="text-center"><?= $this->Paginator->numbers() ?></div>
