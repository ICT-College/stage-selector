<table class="table">
    <thead>
    <tr>
        <th class="table-head"></th>
        <th><?= h(__('Value')); ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th><?= h(__('Position')); ?></th>
        <td><?= h($internship->position->name); ?></td>
    </tr>
    <tr>
        <th><?= h(__('Address')); ?></th>
        <td>
            <strong><?= h($internship->position->company->name); ?></strong><br>
            <?= h($internship->position->company->address); ?><br>
            <?= h($internship->position->company->postcode); ?> <?= h($internship->position->company->city); ?><br>
        </td>
    </tr>
    <tr>
        <th><?= h(__('Telephone')); ?></th>
        <td><?= h($internship->position->company->telephone); ?></td>
    </tr>
    <tr>
        <th><?= h(__('Email')); ?></th>
        <td><?= $this->Html->link($internship->position->company->email, 'mailto:' . $internship->position->company->email1); ?></td>
    </tr>
    </tbody>
    <tbody>
    <tr>
        <th><?= h(__('Had interview')); ?></th>
        <td>
            <?php if ($internship->interviewed): ?>
                <span class="glyphicon glyphicon-ok"></span>
                <?= $this->Html->link(__('Download report'), ['action' => 'report', $internship->id], ['class' => 'btn btn-primary', 'target' => '_blank']); ?>
            <?php else: ?>
                <span class="glyphicon glyphicon-remove"></span> - <?= $this->Html->link(__('Yes, upload report'), ['action' => 'interview', $internship->id], ['class' => 'btn btn-primary']); ?>
            <?php endif; ?>
        </td>
    </tr>
    <tr>
        <th><?= h(__('Accepted')); ?></th>
        <td>
            <?php if ($internship->accepted): ?>
                <span class="glyphicon glyphicon-ok"></span>
            <?php else: ?>
                <span class="glyphicon glyphicon-remove"></span>
            <?php endif; ?>
        </td>
    </tr>
    </tbody>
</table>


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
