<h3><?= h(__('Invite student')) ?></h3>
<p><?= h(__('Here you\'re able to invite students for Stage Selector. When you invite a student, an user will be created for this student and the student will receive an e-mail.')) ?></p>

<?= $this->Form->create($inviteStudent, ['url' => ['action' => 'inviteStudent']]) ?>
<?= $this->Form->input('student_number') ?>

<?= $this->Form->submit(__('Invite'), [
    'class' => 'btn btn-success'
]) ?>
<?= $this->Form->end() ?>

<h3><?= h(__('Sync students')) ?></h3>
<p><?= h(__('Students were last synced at')) ?> <?= $lastStudentsSync ?>.</p>

<?= $this->Form->create($studentsSync, ['type' => 'file', 'url' => ['action' => 'studentsSync']]) ?>
    <?= $this->Form->file('csv') ?>

    <?= $this->Form->submit(__('Submit'), [
        'class' => 'btn btn-success'
    ]) ?>
<?= $this->Form->end() ?>
