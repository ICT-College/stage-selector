<?= __('Thank you for sending in your internship applications'); ?>


<?= __('You\'ve applied for the following positions:'); ?>


<?php foreach ($internshipApplications as $internshipApplication): ?>
    - <?= $internshipApplication->position->company->name; ?> - <?= $internshipApplication->position->study_program->description; ?>

<?php endforeach; ?>

<?= __('You can always come back and change your selection by going to: {0}', $selectorUrl); ?>
