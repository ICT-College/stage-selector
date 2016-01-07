<div class="text-center"><?= $this->Paginator->numbers() ?></div>

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
        <?php foreach ($positions as $position): ?>
            <? debug($postion); ?>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="text-center"><?= $this->Paginator->numbers() ?></div>
