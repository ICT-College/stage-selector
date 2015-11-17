<div class="modal fade" id="inviteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?= $this->Form->create(false, ['url' => ['controller' => 'Students', 'action' => 'invite', 'plugin' => false]]) ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><?= h(__('Invite student')) ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= h(__('Here you\'re able to invite students for Stage Selector. When you invite a student, the student will receive an e-mail with more information.')) ?></p>

                    <?= $this->Form->input('student_number', [
                        'style' => 'width: 300px;'
                    ]) ?>
                    <?= $this->Form->input('period_id', [
                        'style' => 'width: 300px;'
                    ]) ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
<!--                    <button type="button" class="btn btn-primary">Save changes</button>-->
                    <?= $this->Form->button(__('Invite'), [
                        'type' => 'submit',
                        'class' => 'btn btn-primary'
                    ]) ?>
                </div>
            </div>
        <?= $this->Form->end() ?>
    </div>
</div>
