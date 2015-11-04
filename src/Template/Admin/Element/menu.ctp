<div class="collapse navbar-collapse navbar-ex1-collapse navbar-left bs-sidebar">
    <nav>
        <ul class="nav nav-pills nav-stacked">
            <li>
                <?= $this->Html->link(__('Home'), [
                    'plugin' => false,
                    'controller' => 'Pages',
                    'action' => 'display',
                    'home'
                ]) ?>
            </li>

            <li class="dropdown-header"><?= h(__('Management')) ?></li>
            <li role="separator" class="divider"></li>

            <li class="dropdown-header"><?= h(__('Advanced')) ?></li>
            <li role="separator" class="divider"></li>

            <li>
                <?= $this->Html->link(__('Users'), [
                    'plugin' => false,
                    'controller' => 'Users',
                    'action' => 'index'
                ]) ?>
            </li>
        </ul>
    </nav>
</div>
