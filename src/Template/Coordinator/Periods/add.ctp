<?= $this->Html->link(__('Back'), [
    'action' => 'index'
], [
    'class' => 'btn btn-default'
]) ?>

<?= $this->Form->create($period, [
    'align' => [
        'sm' => [
            'left' => 2,
            'middle' => 6,
            'right' => 12
        ],
        'md' => [
            'left' => 2,
            'middle' => 4,
            'right' => 4
        ]
    ]
]) ?>

    <?= $this->Form->input('start') ?>
    <?= $this->Form->input('end') ?>

    <?= $this->Form->submit(__('Submit'), [
        'class' => 'btn btn-success pull-right'
    ]) ?>

<?= $this->Form->end() ?>
