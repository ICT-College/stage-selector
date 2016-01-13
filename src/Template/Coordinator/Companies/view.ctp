<div class="page-header">
    <h2><?= $company->name; ?> <small><?= __('Company information'); ?></small></h2>
</div>

<div class="btn-group">
    <?= $this->Html->link(__('Back'), 'javascript: window.history.back();', [
        'class' => 'btn btn-danger'
    ]) ?>
</div>

<table class="table">
    <thead>
        <tr>
            <th style="width: 20%;">&nbsp;</th>
            <th><?= __('Value'); ?></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th><?= __('Name'); ?></th>
            <td><?= $company->name; ?></td>
        </tr>
        <tr>
            <th><?= __('E-mailaddress'); ?></th>
            <td><?= $company->email; ?></td>
        </tr>
        <tr>
            <th><?= __('Phonenumber'); ?></th>
            <td><?= $company->telephone; ?></td>
        </tr>
        <tr>
            <th><?= __('Website'); ?></th>
            <td><?= $this->Html->link($company->website, $company->website, ['target' => '_BLANK']); ?></td>
        </tr>
        <tr>
            <th><?= __('Address'); ?></th>
            <td>
                <?= $company->address; ?><br/>
                <?= $company->postcode; ?> <?= $company->city; ?>
            </td>
        </tr>
        <tr>
            <th><?= __('Coordinates'); ?></th>
            <td>
                <?= $this->Html->link($company->coordinates, 'https://www.google.nl/maps/place/' . $company->coordinates, ['target' => '_BLANK']); ?>
            </td>
        </tr>
    </tbody>
</table>
