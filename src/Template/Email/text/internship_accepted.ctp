<?= __('Your internship has been accepted by the coordinator and the company. Congratulations.'); ?>


<?= h('Address'); ?>

<?= h($internship->position->company->address); ?>

<?= h($internship->position->company->city); ?>, <?= h($internship->position->company->postcode); ?>

<?= h($internship->position->company->country); ?>
