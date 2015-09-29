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
<!--                <li class="active"><a href="#">1. 0100Dev - Webdeveloper</a></li>-->
<!--                <li class="active"><a href="#">2. CVO Computers - Schoonmaakster</a></li>-->
                <li><a href="#">1.</a></li>
                <li><a href="#">2.</a></li>
            </ul>
        </div>
        <div class="col-md-4">
            <ul class="nav nav-pills nav-stacked nav-selection">
<!--                <li class="active"><a href="#">3. Allahagbar - Boem</a></li>-->
                <li><a href="#">3.</a></li>
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
                    <?= $this->Form->input('company_name', ['label' => __('Company name')]) ?>
                    <?= $this->Form->input('learning_pathway', [
                        'options' => [
                            'BBL',
                            'BOL',
                            'VMBO',
                            'HBO'
                        ],
                        'label' => __('Learning pathway')
                    ]); ?>
                    <?= $this->Form->input('study_program_id', [
                        'type' => 'text',
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
                            <?= $this->Form->input('company_address_street', ['label' => __('Street')]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $this->Form->input('company_address_number', ['label' => __('Number')]) ?>
                        </div>
                    </div>
                    <?= $this->Form->input('company_address_city', ['label' => __('City')]) ?>
                    <?= $this->Form->input('company_address_country', ['label' => __('Country')]) ?>

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
                    <?= $this->Form->input('company_id', ['type' => 'text', 'label' => __('Code company')]) ?>
                    <?= $this->Form->input('brin', ['label' => __('BRIN number')]) ?>

                    <?= $this->Form->submit(__('Search'), ['class' => 'btn btn-primary', 'style' => 'width: 100%; margin-top: 25px;']) ?>
                </div>

                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</div>

<table class="table table-hover positions" data-page="1">
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
        <tr><td colspan="5">Geen zoekresultaten</td></tr>
    </tbody>
</table>

<div class="text-center" style="display: none;">
    <ul class="pagination">

    </ul>
</div>

<div class="modal fade position-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title study-program-title"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Functie</strong>: <span class="study-program-description"></span><br/>
                        <strong>Omschrijving</strong>: <br/>
                        <p class="position-description"></p>

                        <address>
                            <strong><?= h(__('Adres')); ?></strong><br/>
                            <span class="company-address-address"></span><br/>
                            <span class="company-address-postcode"></span>, <span class="company-address-city"></span>
                        </address>

                        <strong>Email</strong>: <span class="company-email"></span><br/>
                        <strong>Website</strong>: <a class="company-website"></a><br/>
                        <strong>Telephone</strong>: <span class="company-telephone"></span>
                    </div>
                    <div class="col-md-6">
                        <iframe style="width: 100%;"></iframe>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <ol class="qualification-parts"></ol>
                    </div>
                    <div class="col-md-6">
                        <ol class="qualification-parts"></ol>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Sluit</button>
                <button type="button" class="btn btn-success position-select">Voeg toe</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
