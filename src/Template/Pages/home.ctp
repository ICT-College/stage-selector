<?php $this->assign('title', 'Voorpagina'); ?>

<p><?= $loggedUser['firstname'] ?> <?= $loggedUser['lastname'] ?> (<?= $loggedUser['email'] ?>)</p>

<?= $this->Form->postLink(__('Logout'), ['controller' => 'Users', 'action' => 'logout']);
