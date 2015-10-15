<div class="collapse navbar-collapse navbar-ex1-collapse navbar-left bs-sidebar">
    <nav>
        <ul class="nav nav-pills nav-stacked">
            <li>
                <?= $this->Html->link(__('Home'), [
                    'controller' => 'Pages',
                    'action' => 'display',
                    'home'
                ]) ?>
            </li>

            <li class="dropdown-header"><?= h(__('Management')) ?></li>
            <li role="separator" class="divider"></li>

            <li>
                <?= $this->Html->link(__('Students'), [
                    'controller' => 'Students',
                    'action' => 'index'
                ]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Companies'), [
                    'controller' => 'Companies',
                    'action' => 'index',
                    'prefix' => 'admin'
                ]) ?>
            </li>

            <li class="dropdown-header"><?= h(__('Advanced')) ?></li>
            <li role="separator" class="divider"></li>

            <li>
                <?= $this->Html->link(__('Users'), [
                    'controller' => 'Users',
                    'action' => 'index'
                ]) ?>
            </li>
        </ul>
    </nav>
</div>
