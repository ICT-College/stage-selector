<hr>
<?= $this->Form->create(null, [
    'align' => [
        'sm' => [
            'left' => 2,
            'middle' => 6,
            'right' => 12
        ],
        'md' => [
            'left' => 4,
            'middle' => 8,
            'right' => 4
        ]
    ]
]) ?>
    <?= $this->Form->input('q', ['label' => 'Search']) ?>
    <?= $this->Form->button(__('Filter'), ['type' => 'submit', 'class' => 'btn btn-success pull-right']) ?>
<?= $this->Form->end() ?>
