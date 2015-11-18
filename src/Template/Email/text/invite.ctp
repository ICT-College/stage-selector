<?= __('Congratulations! You\'re now allowed to get an internship!'); ?>


<?= __('You can register yourself over here: {0}', \Cake\Routing\Router::url(['_name' => 'users_activate', $user->activation_token], true)); ?>
