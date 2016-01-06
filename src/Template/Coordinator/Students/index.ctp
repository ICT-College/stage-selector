<?= $this->start('search') ?>
    <?= $this->element('search') ?>
<?= $this->end() ?>
<div class="text-center"><?= $this->Paginator->numbers() ?></div>

<table class="table table-hover">
    <thead>
    <tr>
        <th><?= $this->Paginator->sort('student_number', __('ID')) ?></th>
        <th><?= $this->Paginator->sort('firstname', __('Name')) ?></th>
        <th><?= $this->Paginator->sort('email', __('E-mail')) ?></th>
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
                    $student->student_id
                ]) ?>
            </td>
            <td><?= $student->email ?></td>
            <td>
                <?= $this->Html->link(__('Edit'), [
                    'action' => 'edit',
                    $student->student_id
                ], [
                    'class' => 'btn btn-default'
                ]) ?>
                <?= $this->Form->postLink(__('Delete'), [
                    'action' => 'delete',
                    $student->student_id
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
