<h1><?= h(__('Internship')); ?></h1>

<?= $this->Html->link(__('Back'), [
    'action' => 'index'
], [
    'class' => 'btn btn-default'
]) ?>
<br/><br/>

<h2><?= h(__('Period')); ?></h2>

<table class="table">
    <tr>
        <th><?= h(__('Start')); ?></th>
        <td><?= h($internship->period->start); ?></td>
    </tr>
    <tr>
        <th><?= h(__('End')); ?></th>
        <td><?= h($internship->period->end); ?></td>
    </tr>
</table>

<h2><?= h(__('Student')); ?></h2>

<table class="table">
    <tr>
        <th><?= h(__('Name')); ?></th>
        <td><?= h($internship->user->name); ?></td>
    </tr>
</table>

<h2><?= h(__('Position')); ?></h2>

<table class="table">
    <tr>
        <th><?= h(__('Description')); ?></th>
        <td><?= h($internship->position->study_program->description); ?></td>
    </tr>
    <tr>
        <th><?= h(__('Company')); ?></th>
        <td><?= h($internship->position->company->name); ?></td>
    </tr>
</table>

