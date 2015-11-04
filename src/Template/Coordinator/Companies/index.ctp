<?= $this->start('search') ?>
    <?= $this->element('search') ?>
<?= $this->end() ?>
<div class="text-center"><?= $this->Paginator->numbers() ?></div>

<table class="table table-hover">
    <thead>
    <tr>
        <th><?= $this->Paginator->sort('stagemarkt_id', __('ID')) ?></th>
        <th><?= $this->Paginator->sort('name', __('Name')) ?></th>
        <th><?= h(__('Address')) ?></th>
        <th><?= h(__('Contact')) ?></th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($companies as $company): ?>
        <tr>
            <td><?= $company->stagemarkt_id ?></td>
            <td>
                <?= $this->Html->link($company->name, [
                    'action' => 'view',
                    $company->id
                ]) ?>
            </td>
            <td>
                <?= $company->address ?><br/>
                <?= $company->postcode ?> <?= $company->city ?>
            </td>
            <td>
                <?= $company->telephone ?><br/>
                <?= $company->email ?>
            </td>
            <td>
                <?= $this->Html->link(__('Edit'), [
                    'action' => 'edit',
                    $company->id
                ], [
                    'class' => 'btn btn-default'
                ]) ?>
                <?= $this->Html->link(__('Delete'), [
                    'action' => 'delete',
                    $company->id
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
