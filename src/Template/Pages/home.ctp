<?php $this->assign('title', 'Voorpagina'); ?>
<h2><?= __('Welcome to the Stage Selector.'); ?></h2>

<p><?= __('You\'re currently logged in as {0} ({1}). Choose one option below.', $loggedUser['name'], $loggedUser['email']); ?></p>

<?= $this->Html->link(__('Stage Selector'), '/selector'); ?><br/><br/>
<?= $this->Form->postLink(__('Logout'), ['controller' => 'Users', 'action' => 'logout']); ?>
