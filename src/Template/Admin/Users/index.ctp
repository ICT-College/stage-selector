<table class="table table-hover">
    <thead>
        <tr>
            <th><?= h(__('ID')) ?></th>
            <th><?= h(__('Student')) ?></th>
            <th><?= h(__('Name')) ?></th>
            <th><?= h(__('E-mail')) ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= $user->id ?></td>
                <td><?= ((empty($user->student_number)) ? '<i>' . h(__('No student')) . '</i>' : $user->student_number) ?></td>
                <td>
                    <?= $this->Html->link($user->name, [
                        'action' => 'view',
                        $user->id
                    ]) ?>
                </td>
                <td><?= $user->email ?></td>
                <td>
                    <?= $this->Html->link(__('Edit'), [
                        'action' => 'edit',
                        $user->id
                    ], [
                        'class' => 'btn btn-default'
                    ]) ?>
                    <?= $this->Html->link(__('Delete'), [
                        'action' => 'delete',
                        $user->id
                    ], [
                        'confirm' => __('Are you sure?'),
                        'class' => 'btn btn-danger'
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
