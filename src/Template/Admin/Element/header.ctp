<nav class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Stage Selector</a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <?= $this->Html->link(__('Invite'), '#', [
                        'data-toggle' => 'modal',
                        'data-target' => '#inviteModal'
                    ]) ?>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $loggedUser['firstname'] ?> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <?= $this->Form->postLink(__('Logout'), [
                                'controller' => 'Users',
                                'action' => 'logout',
                                'prefix' => false,
                                'plugin' => false
                            ]) ?>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
