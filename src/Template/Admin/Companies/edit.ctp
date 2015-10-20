<?php
use SameerShelavale\PhpCountriesArray\CountriesArray;
?>
<?= $this->Html->link(__('Back'), [
    'action' => 'index'
], [
    'class' => 'btn btn-default'
]) ?>

<?= $this->Form->create($company, [
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

    <?= $this->Form->input('name') ?>

    <?= $this->Form->input('address') ?>
    <?= $this->Form->input('postcode') ?>
    <?= $this->Form->input('city') ?>
    <?= $this->Form->input('country', [
        'options' => CountriesArray::get('alpha2', 'name')
    ]) ?>

    <?= $this->Form->input('telephone') ?>
    <?= $this->Form->input('email') ?>

    <?= $this->Form->submit(__('Submit'), [
        'class' => 'btn btn-success pull-right'
    ]) ?>

<?= $this->Form->end() ?>
