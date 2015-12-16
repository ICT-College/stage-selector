<h2><?= __('Welcome to the Stage Selector.'); ?></h2>
<p><?= __('Here you can activate your Stage Selector account by setting a password. After you\'ve set a password, your account is activated and you will being redirected to the selector where you\'re able to select an internship. Thanks for using Stage Selector and good luck selecting an internship!'); ?></p>
<?= $this->Form->create($user); ?>
<?= $this->Form->input('password'); ?>
<?= $this->Form->input('password_verification', ['type' => 'password']); ?>
<?= $this->Form->submit(__('Activate')); ?>
<?= $this->Form->end(); ?>
