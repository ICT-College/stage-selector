<?= $this->Html->link(__('Back'), [
    'action' => 'index'
], [
    'class' => 'btn btn-default'
]) ?>&nbsp;
<?= $this->Form->postLink(__('Invite'), [
    'controller' => 'Users',
    'action' => 'invite',
    $student->student_number
], [
    'class' => 'btn btn-default'
]) ?>
<br/><br/>
<table class="table">
    <tbody>
        <?php foreach ($student->toArray() as $key => $value): ?>
            <tr>
                <td><?= $key ?></td>
                <td><?= $value ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
