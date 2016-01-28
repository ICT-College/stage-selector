<table class="table table-striped">
    <thead>
        <tr>
            <th style="width: 25%;"><?= __('Period'); ?></th>
            <th style="width: 60%;"><?= __('Internship'); ?></th>
            <th style="width: 15%;">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($periods)): ?>
            <tr>
                <td colspan="3"><?= __('No periods assigned to you yet.'); ?></td>
            </tr>
        <?php else: ?>
            <?php foreach ($periods as $period): ?>
                <tr>
                    <td><?= $period->title; ?></td>
                    <td>
                        <?php if (empty($period->internships[0]->position)): ?>
                            <?= __('No internship accepted yet by the internship coordinator.'); ?>
                        <?php else: ?>
                            <?= __('{0} at {1}', $period->internships[0]->position->study_program->description, $period->internships[0]->position->company->name); ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($period->internships[0]->position)): ?>
                            <?= $this->Html->link(__('View progress'), [
                                'controller' => 'Internships',
                                'action' => 'view',
                                $period->internships[0]->position->id
                            ], [
                                'class' => 'btn btn-success'
                            ]); ?>
                        <?php else: ?>
                            <?= $this->Html->link(__('View selection'), [
                                '_name' => 'selector',
                                $period->id
                            ], [
                                'class' => 'btn btn-primary'
                            ]); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
