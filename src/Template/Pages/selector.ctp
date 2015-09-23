<?= $this->Html->script(['select.js', 'awesomplete.min.js'], ['block' => true]) ?>
<?= $this->Html->css(['awesomplete.css'], ['block' => true]) ?>
<?= $this->start('header') ?>
<div class="header clearfix">
    <div class="row">
        <div class="col-md-3">
            <h3 class="text-muted">Stage Selector</h3>
        </div>

        <div class="col-md-4">
            <ul class="nav nav-pills nav-stacked nav-selection">
                <li class="active"><a href="#">1. 0100Dev - Webdeveloper</a></li>
                <li class="active"><a href="#">2. CVO Computers - Schoonmaakster</a></li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul class="nav nav-pills nav-stacked nav-selection">
                <li class="active"><a href="#">3. Allahagbar - Boem</a></li>
                <li><a href="#">4.</a></li>
            </ul>
        </div>

        <div class="col-md-1">
            <?= $this->Form->button('<span class="glyphicon glyphicon-chevron-right"></span>', [
                'type' => 'submit',
                'escape' => false,
                'disabled' => 'disabled',
                'class' => 'btn btn-success pull-right',
                'style' => 'height: 50px;'
            ]) ?>
        </div>
    </div>
</div>
<?= $this->end() ?>

<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="footersHeading">
        <h4 class="panel-title">
            Filters
            <span class="glyphicon glyphicon-chevron-up"></span>
        </h4>
    </div>
    <div id="filters" class="panel-collapse collapse" role="tabpanel" aria-labelledby="footersHeading">
        <div class="panel-body">
            <div class="row">
                <?= $this->Form->create(false, ['type' => 'get', 'id' => 'filter']) ?>

                <div class="col-md-5">
                    <?= $this->Form->input('company_name') ?>
                    <?= $this->Form->input('learn_route', [
                        'options' => [
                            'BBL',
                            'BOL',
                            'VMBO',
                            'HBO'
                        ]
                    ]); ?>
                    <?= $this->Form->input('crebo', [
                        'label' => [
                            'text' => __('CREBO number/name') . ' <span class="glyphicon glyphicon-info-sign"><span>',
                            'data-toggle' => 'tooltip',
                            'title' => __('You can either enter a CREBO number of enter a crebo name.'),
                            'escape' => false
                        ]
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-8">
                            <?= $this->Form->input('street') ?>
                        </div>
                        <div class="col-md-4">
                            <?= $this->Form->input('number') ?>
                        </div>
                    </div>
                    <?= $this->Form->input('city') ?>
                    <?= $this->Form->input('country') ?>

                    <?= $this->Form->input('search_in_area', [
                        'type' => 'checkbox',
                        'checked',
                        'label' => [
                            'text' => __('Search in area') . ' <span class="glyphicon glyphicon-info-sign"><span>',
                            'data-toggle' => 'tooltip',
                            'title' => __('When you select this, we search around the location given above. When you unselect this, the company MUST be located at the given location above.'),
                            'escape' => false
                        ]
                    ]) ?>
                </div>

                <div class="col-md-3">
                    <?= $this->Form->input('code_company', ['label' => __('Code company')]) ?>
                    <?= $this->Form->input('brin', ['label' => __('BRIN number')]) ?>

                    <?= $this->Form->submit(__('Search'), ['class' => 'btn btn-primary', 'style' => 'width: 100%; margin-top: 25px;']) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<table class="table table-hover">
    <thead>
    <tr>
        <th></th>
        <th><?= __('Job') ?></th>
        <th><?= __('Company') ?></th>
        <th><?= __('Address') ?></th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    <tr data-id="1">
        <th scope="row">1</th>
        <td>
            Applicatie ontwikkelaar (BBL)<br/>
            Richting ICT
        </td>
        <td>
            0100Dev<br/>
            Tel: 0646248664
        </td>
        <td>
            Neerlandstraat 32<br/>
            5662JC Geldrop
        </td>
        <td>
            <?= $this->Html->link('<span class="glyphicon glyphicon-plus"></span>', '#toggle-1', [
                'data-toggle' => 'selection',
                'data-state' => 'add',
                'class' => 'btn btn-success pull-right',
                'escape' => false
            ]) ?>
        </td>
    </tr>
    <tr data-id="2">
        <th scope="row">2</th>
        <td>
            Systeem beheerder (BOL)<br/>
            Richting Zorg en natura
        </td>
        <td>
            CVO-Technologies<br/>
            Tel: 06132345678
        </td>
        <td>
            Limburgertje 20<br/>
            8888BB Weert
        </td>
        <td>
            <?= $this->Html->link('<span class="glyphicon glyphicon-plus"></span>', '#toggle-2', [
                'data-toggle' => 'selection',
                'data-state' => 'add',
                'class' => 'btn btn-success pull-right',
                'escape' => false
            ]) ?>
        </td>
    </tr>
    <tr data-id="3">
        <th scope="row">3</th>
        <td>
            Schoonmaakster (VMBO)<br/>
            Richting Vies en was
        </td>
        <td>
            CVO Computers<br/>
            Tel: 06132345678
        </td>
        <td>
            Limburgerstraat 12<br/>
            9999AA Herten
        </td>
        <td>
            <?= $this->Html->link('<span class="glyphicon glyphicon-plus"></span>', '#toggle-3', [
                'data-toggle' => 'selection',
                'data-state' => 'add',
                'class' => 'btn btn-success pull-right',
                'escape' => false
            ]) ?>
        </td>
    </tr>
    </tbody>
</table>