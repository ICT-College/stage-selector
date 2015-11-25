<p><strong><?= __('Welcome to the Stage Selector.'); ?></strong></p>
<p><?= __('Here you can set a password for your Stage Selector account. After you\'ve set a password, you will being redirected to the selector where you\'re able to select an internship.'); ?></p>
<?= $this->Form->create($user); ?>
<?= $this->Form->input('password'); ?>
<?= $this->Form->input('password_verification', ['type' => 'password']); ?>
<?= $this->Form->submit(__('Activate')); ?>
<?= $this->Form->end(); ?>
