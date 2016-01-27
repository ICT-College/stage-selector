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

<?php
$activeTab = 0;
$disabledTabs = [];
$tabs = [
    [
        'id' => 'plan-interview',
        'title' => __('Step 1: Plan interview'),
    ],
    [
        'id' => 'upload-report',
        'title' => __('Step 2: Upload report'),
    ],
    [
        'id' => 'report-review',
        'title' => __('Step 3: Report review')
    ]
];
if ($internship->planned_interview_date) {
    $activeTab = 1;
    $disabledTabs[] = 0;
}
if ($internship->interviewed) {
    $activeTab = 2;
    $disabledTabs[] = 1;
}
?>
<div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($tabs as $tabIndex => $tab): ?>
            <li role="presentation" class="<?= h(($tabIndex === $activeTab) ? 'active' : ''); ?> <?= h((in_array($tabIndex, $disabledTabs)) ? 'disabled' : ''); ?>"><a <?php if (!in_array($tabIndex, $disabledTabs)): ?>href="#<?= h($tab['id']); ?>" data-toggle="tab"<?php endif; ?> aria-controls="home" role="tab"><?= h($tab['title']); ?></a></li>
        <?php endforeach; ?>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <?php $tabIndex = 0; ?>
        <div role="tabpanel" class="tab-pane <?= h(($tabIndex++ === $activeTab) ? 'active' : ''); ?>" id="plan-interview">
            <?= $this->Form->create($internship, [
                'url' => [
                    'action' => 'planInterview'
                ]
            ]); ?>
            <?= $this->Form->input('planned_interview_date'); ?>
            <?= $this->Form->submit(); ?>
            <?= $this->Form->end(); ?>
        </div>
        <div role="tabpanel" class="tab-pane <?= h(($tabIndex++ === $activeTab) ? 'active' : ''); ?>" id="upload-report">
            <?= $this->Form->create($internship, [
                'url' => [
                    'action' => 'interview'
                ],
                'type' => 'file'
            ]); ?>
            <?= $this->Form->input('report', ['type' => 'file']); ?>
            <?= $this->Form->input('contact_email', [
                'help' => __('You can use <strong>{0}</strong> as email address', h($internship->position->company->email))
            ]); ?>
            <?= $this->Form->submit(); ?>
            <?= $this->Form->end(); ?>
        </div>
        <div role="tabpanel" class="tab-pane <?= h(($tabIndex++ === $activeTab) ? 'active' : ''); ?>" id="report-review">
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
        </div>
    </div>
</div>
