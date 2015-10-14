<?php
if (!$this->exists('header')) {
    $this->start('header');
    ?>
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
        </div>
    </nav>
    <?php
    $this->end();
}
?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $this->fetch('title') ?> - Stage Selector
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->fetch('css') ?>

    <?= $this->Html->css('bootstrap.min.css') ?>

    <?= $this->Html->script('jquery.min.js') ?>
    <?= $this->Html->script('bootstrap.min.js') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
<?= $this->fetch('header') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-xs-0 col-sm-2 col-lg-2">
            <div class="collapse navbar-collapse navbar-ex1-collapse navbar-left bs-sidebar">
                <nav>
                    <ul class="nav nav-pills nav-stacked">
                        <li>
                            <?= $this->Html->link(__('Users'), [
                                'controller' => 'Users',
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
                        <li>
                            <?= $this->Html->link(__('Students'), [
                                'controller' => 'Students',
                                'action' => 'index'
                            ]) ?>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="col-xs-12 col-sm-10 col-lg-10">
            <?= $this->Flash->render(); ?>

            <?= $this->fetch('content') ?>
        </div>
    </div>
</div>

</body>
</html>
