<div class="text-center"><?= $this->Paginator->numbers() ?></div>

<table class="table table-hover">
    <thead>
    <tr>
        <th><?= h(__('ID')) ?></th>
        <th><?= h(__('Name')) ?></th>
        <th><?= h(__('E-mail')) ?></th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($students as $student): ?>
        <tr>
            <td><?= $student->student_number ?></td>
            <td>
                <?= $this->Html->link($student->name, [
                    'action' => 'view',
                    $student->id
                ]) ?>
            </td>
            <td><?= $student->email ?></td>
            <td>
                <?= $this->Html->link(__('Edit'), [
                    'action' => 'edit',
                    $student->id
                ], [
                    'class' => 'btn btn-default'
                ]) ?>
                <?= $this->Html->link(__('Delete'), [
                    'action' => 'delete',
                    $student->id
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
