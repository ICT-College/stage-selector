<table class="table">
    <caption><?= h(__('Accepted by')); ?></caption>
    <thead>
    <tr>
        <th></th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th><?= h(__('Student')); ?></th>
        <td><?= h(($internship->accepted_by_student) ? __('Yes') : __('No')); ?></td>
        <td>
            <?= $this->Form->postLink(__('Accept'), ['action' => 'accept', $internship->id], ['class' => 'btn btn-primary']); ?>
        </td>
    </tr>
    <tr>
        <th><?= h(__('Coordinator')); ?></th>
        <td><?= h(($internship->accepted_by_coordinator) ? __('Yes') : __('No')); ?></td>
        <td></td>
    </tr>
    <tr>
        <th><?= h(__('Company')); ?></th>
        <td><?= h(($internship->accepted_by_company) ? __('Yes') : __('No')); ?></td>
        <td></td>
    </tr>
    </tbody>
</table>

<?php debug($internship); ?>
