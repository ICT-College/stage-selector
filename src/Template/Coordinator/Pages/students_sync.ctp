<?php use Cake\Cache\Cache;

$lastStudentsSync = Cache::read('students_sync');
?>
<h3><?= h(__('Sync students')) ?></h3>
<?php if ($lastStudentsSync): ?>
    <p><?= h(__('Students were last synced at')) ?> <?= $lastStudentsSync->i18nFormat(null, 'Europe/Amsterdam') ?>.</p>
<?php else: ?>
    <p><?= h(__('Students aren\'t synced yet')) ?>.</p>
<?php endif; ?>

<?= $this->Form->create(false, ['type' => 'file', 'url' => ['controller' => 'students', 'action' => 'synchronize', 'plugin' => false]]) ?>
<?= $this->Form->file('csv') ?>
<br/>
<?= $this->Form->submit(__('Submit'), [
    'class' => 'btn btn-success'
]) ?>
<?= $this->Form->end() ?>
