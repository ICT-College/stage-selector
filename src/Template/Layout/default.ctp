<?php
if (!$this->exists('header')):
    $this->start('header');
        ?>
        <div class="header clearfix">
            <h3 class="text-muted">Stage Selector</h3>
        </div>
        <?php
    $this->end();
endif;
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
    <?= $this->Html->css('theme.css') ?>

    <?= $this->Html->script('jquery.min.js') ?>
    <?= $this->Html->script('bootstrap.min.js') ?>
    <?= $this->Html->script('autocomplete') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <div class="container-fluid">
        <?= $this->fetch('header') ?>
    </div>

    <div class="container">
        <?= $this->Flash->render() ?>

        <?= $this->fetch('content') ?>

        <footer class="footer">
            <p>
                Gegevens over de stageplekken worden medemogelijk gemaakt door <?= $this->Html->link('Stagemarkt', 'http://stagemarkt.nl/') ?>. <br/>
                Ontwikkeld door Marlin Cremers & Wouter van Os van het ICT College (ROC Ter AA) - Copyright ROC Ter AA &copy; <?= ((date('Y') > 2015) ? date('Y') . ' - ' : '') . date('Y') ?>
            </p>
        </footer>

    </div> <!-- /container -->

    <div class="modal fade loading-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="progress progress-striped active" style="margin-bottom: 0;">
                        <div class="progress-bar" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

</body>
</html>
