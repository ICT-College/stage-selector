<?php $this->assign('title', 'Voorpagina'); ?>
<h2><?= __('Welcome to the Stage Selector.'); ?></h2>

<p><?= __('You\'re currently logged in as {0} ({1}). In the table below you\'ll see a list of periods and internships you\'ve done or you need to do.', $loggedUser['name'], $loggedUser['email']); ?></p>

<?= $this->cell('Periods', [ $loggedUser ]); ?>

<?= $this->Form->postLink(__('Logout'), ['controller' => 'Users', 'action' => 'logout']); ?>
