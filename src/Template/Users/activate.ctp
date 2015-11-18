<?= $this->Form->create($user); ?>
<?= $this->Form->input('password'); ?>
<?= $this->Form->input('password_verification', ['type' => 'password']); ?>
<?= $this->Form->submit(__('Activate')); ?>
<?= $this->Form->end(); ?>
