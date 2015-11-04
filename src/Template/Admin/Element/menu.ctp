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

            <li>
                <?= $this->Html->link(__('Students'), [
                    'plugin' => false,
                    'controller' => 'Students',
                    'action' => 'index'
                ]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Companies'), [
                    'plugin' => false,
                    'controller' => 'Companies',
                    'action' => 'index',
                    'prefix' => 'admin'
                ]) ?>
            </li>
            <li>
                <?= $this->Html->link(__('Periods'), [
                    'plugin' => false,
                    'controller' => 'Periods',
                    'action' => 'index',
                    'prefix' => 'admin'
                ]) ?>
            </li>

            <li class="dropdown-header"><?= h(__('Advanced')) ?></li>
            <li role="separator" class="divider"></li>

            <li>
                <?= $this->Html->link(__('Sync students'), [
                    'plugin' => false,
                    'controller' => 'Pages',
                    'action' => 'display',
                    'students_sync'
                ]) ?>
            </li>

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
