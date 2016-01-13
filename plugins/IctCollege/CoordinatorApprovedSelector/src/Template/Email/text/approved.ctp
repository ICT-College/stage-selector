<?= __('You can go to {0} as {1}', $internship->position->company->name, $internship->position->study_program->description); ?>


<?= __('Address:'); ?>

<?= h($internship->position->company->address); ?>

<?= h($internship->position->company->postcode); ?>, <?= h($internship->position->company->city); ?>


<?= h($internship->position->company->telephone); ?>

<?= h($internship->position->company->email); ?>


<?= __('You can go on a interview and go to {0} to upload your report.', $internshipUrl); ?>
