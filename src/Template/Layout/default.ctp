<?php
if (!$this->exists('header')) {
    $this->start('header');
        ?>
        <div class="header clearfix">
            <h3 class="text-muted">Stage Selector</h3>
        </div>
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

    <?= $this->Html->css('bootstrap.min.css') ?>
    <?= $this->Html->css('theme.css') ?>

    <?= $this->Html->script('jquery.min.js') ?>
    <?= $this->Html->script('bootstrap.min.js') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <div class="container">
        <?= $this->fetch('header') ?>
        <?= $this->fetch('content') ?>

        <footer class="footer">
            <p>
                Gegevens over de stageplekken worden medemogelijk gemaakt door <?= $this->Html->link('Stagemarkt', 'http://stagemarkt.nl/') ?>. <br/>
                Ontwikkeld door Marlin Cremers & Wouter van Os van het ICT College (ROC Ter AA) - Copyright ROC Ter AA &copy; <?= ((date('Y') > 2015) ? date('Y') . ' - ' : '') . date('Y') ?>
            </p>
        </footer>

    </div> <!-- /container -->

</body>
</html>
