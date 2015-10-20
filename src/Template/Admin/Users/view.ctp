<?= $this->Html->link(__('Back'), [
    'action' => 'index'
], [
    'class' => 'btn btn-default'
]) ?>&nbsp;
<?php if (!empty($user->student_number)): ?>
    <?= $this->Form->postLink(__('Invite'), [
        'action' => 'invite',
        $user->student_number
    ], [
        'class' => 'btn btn-default'
    ]) ?>
<?php endif; ?>
<br/><br/>
<table class="table">
    <tbody>
        <?php foreach ($user->toArray() as $key => $value): ?>
            <tr>
                <td><?= $key ?></td>
                <td><?= $value ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
