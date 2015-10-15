<?php
if (!$this->exists('header')) {
    $this->start('header');
        echo $this->element('header');
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
            <?= $this->element('menu') ?>
        </div>
        <div class="col-xs-12 col-sm-10 col-lg-10">
            <?= $this->Flash->render() ?>

            <?= $this->fetch('content') ?>
        </div>
    </div>
</div>

<?= $this->element('invite') ?>

</body>
</html>
