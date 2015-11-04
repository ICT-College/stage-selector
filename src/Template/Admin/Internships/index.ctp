<?= $this->start('search') ?>
    <?= $this->element('search') ?>
<?= $this->end() ?>
<div class="text-center"><?= $this->Paginator->numbers() ?></div>

<table class="table table-hover">
    <thead>
    <tr>
        <th><?= h(__('Name')) ?></th>
        <th><?= h(__('Position')) ?></th>
        <th><?= h(__('End')) ?></th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($internships as $internship): ?>
        <tr>
            <td>
                <?= $this->Html->link($internship->user->name, [
                    'action' => 'view',
                    $internship->id
                ]) ?>
            </td>
            <td><?= h(__('{0} at {1}', $internship->position->study_program->description, $internship->position->company->name)) ?></td>
            <td>
                <?= $this->Html->link(__('Edit'), [
                    'action' => 'edit',
                    $internship->id
                ], [
                    'class' => 'btn btn-default'
                ]) ?>
                <?= $this->Html->link(__('Delete'), [
                    'action' => 'delete',
                    $internship->id
                ], [
                    'confirm' => __('Are you sure?'),
                    'class' => 'btn btn-danger'
                ]) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="text-center"><?= $this->Paginator->numbers() ?></div>
