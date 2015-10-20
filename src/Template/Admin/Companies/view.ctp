<?= $this->Html->link(__('Back'), [
    'action' => 'index'
], [
    'class' => 'btn btn-default'
]) ?>
<br/><br/>
<table class="table">
    <tbody>
        <?php foreach ($company->toArray() as $key => $value): ?>
            <tr>
                <td><?= $key ?></td>
                <td><?= $value ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
