<h3><?= h(__('Invite student')) ?></h3>
<p><?= h(__('Here you\'re able to invite students for Stage Selector. When you invite a student, an user will be created for this student and the student will receive an e-mail.')) ?></p>

<?= $this->Form->create($inviteStudent, ['url' => ['action' => 'inviteStudent']]) ?>
<?= $this->Form->input('student_number', [
    'style' => 'width: 150px;'
]) ?>

<?= $this->Form->submit(__('Invite'), [
    'class' => 'btn btn-success'
]) ?>
<?= $this->Form->end() ?>

<h3><?= h(__('Sync students')) ?></h3>
<?php if ($lastStudentsSync): ?>
    <p><?= h(__('Students were last synced at')) ?> <?= $lastStudentsSync->i18nFormat(null, 'Europe/Amsterdam') ?>.</p>
<?php else: ?>
    <p><?= h(__('Students aren\'t synced yet')) ?>.</p>
<?php endif; ?>

<?= $this->Form->create($studentsSync, ['type' => 'file', 'url' => ['action' => 'studentsSync']]) ?>
    <?= $this->Form->file('csv') ?>
    <br/>
    <?= $this->Form->submit(__('Submit'), [
        'class' => 'btn btn-success'
    ]) ?>
<?= $this->Form->end() ?>
