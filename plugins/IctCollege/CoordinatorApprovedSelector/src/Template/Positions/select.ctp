<?= $this->Html->script(['handlebars.min-latest.js', 'awesomplete.min.js', 'bootstrap-slider.min.js', 'IctCollege/CoordinatorApprovedSelector.select'], ['block' => true]) ?>
<?= $this->Html->css(['awesomplete.css', 'bootstrap-slider.min.css'], ['block' => true]) ?>
<?= $this->start('header') ?>
<div class="header clearfix">
    <div class="row">
        <div class="col-md-3">
            <h3 class="text-muted">Stage Selector</h3>
        </div>

        <div class="selection">
            <div class="col-md-4">
                <ul class="nav nav-pills nav-stacked nav-selection">
                    <li><a href="#">1.</a></li>
                    <li><a href="#">2.</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <ul class="nav nav-pills nav-stacked nav-selection">
                    <li><a href="#">3.</a></li>
                    <li><a href="#">4.</a></li>
                </ul>
            </div>
        </div>

        <script id="selection" type="text/x-handlebars-template">
            {{#to 4 selection}}
                {{#if (modulo current 2 1)}}
                    <div class="col-md-4">
                        <ul class="nav nav-pills nav-stacked nav-selection">
                {{/if}}

                {{#if exists}}
                    {{#if accepted_coordinator}}
                        <li class="disabled">
                            <a href="#">
                                {{current}}. {{position.company.name}} - {{position.study_program.description}}
                            </a>
                        </li>
                    {{else}}
                        <li data-position-id="{{position.id}}" class="active">
                            <a href="#">
                                {{current}}. {{position.company.name}} - {{position.study_program.description}}
                                <button type="button" class="close" data-toggle="selection" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </a>
                        </li>
                    {{/if}}
                {{else}}
                    <li><a href="#">{{current}}.</a></li>
                {{/if}}

                {{#if (modulo current 2 0)}}
                        </ul>
                    </div>
                {{/if}}
            {{/to}}
        </script>

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
                <?= $this->Form->create(false) ?>

                <div class="col-md-5">
                    <?= $this->Form->input('description', ['label' => __('Description')]) ?>
                    <?= $this->Form->input('company_name', [
                        'type' => 'autocomplete',
                        'label' => __('Company name'),
                        'autocompleteUrl' => '/api/companies.json',
                        'autocompleteValue' => 'name',
                        'autocompleteKey' => false,
                        'autocompleteStrict' => false
                    ]) ?>
                    <?= $this->Form->input('study_program_id', [
                        'type' => 'autocomplete',
                        'label' => [
                            'text' => __('CREBO number/name') . ' <span class="glyphicon glyphicon-info-sign"><span>',
                            'data-toggle' => 'tooltip',
                            'title' => __('You can either enter a CREBO number of enter a crebo name.'),
                            'escape' => false
                        ],
                        'autocompleteUrl' => '/api/study_programs.json',
                        'autocompleteValue' => 'description'
                    ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $this->Form->input('company_address', ['label' => __('Adres')]) ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->Form->input('company_postcode', ['label' => __('Postcode')]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $this->Form->input('company_city', ['label' => __('City')]) ?>
                        </div>
                    </div>

                    <?= $this->Form->input('radius', [
                        'data-slider-min' => 5,
                        'data-slider-max' => 50,
                        'data-slider-step' => 1,
                        'data-slider-value' => 10,
                        'value' => 10,
                        'label' => [
                            'text' => __('Search in area') . ' <span class="glyphicon glyphicon-info-sign"><span>',
                            'data-toggle' => 'tooltip',
                            'title' => __('To search in a radius, you need to have filled in address, postcode and/or city.'),
                            'escape' => false
                        ]
                    ]) ?>
                </div>

                <div class="col-md-3">
                    <?= $this->Form->input('stagemarkt_id', ['type' => 'text', 'label' => __('Code company')]) ?>
                    <?= $this->Form->input('learning_pathway', [
                        'options' => [
                            'BBL' => 'BBL',
                            'BOL' => 'BOL',
                            'VMBO' => 'VMBO',
                            'HBO' => 'HBO'
                        ],
                        'label' => __('Learning pathway')
                    ]); ?>

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

    </tbody>

    <script id="positions" type="text/x-handlebars-template">
        {{#each positions}}
            <tr data-position-id="{{id}}">
                <th scope="row">{{availability}}</th>
                <td>{{study_program.description}}</td>
                <td>
                    {{company.name}}<br/>
                    Tel: {{company.telephone}}
                </td>
                <td>
                    {{company.address}}<br/>
                    {{company.postcode}} {{company.city}}
                </td>
                <td>
                    <div class="pull-right">
                        <a href="#" data-toggle="modal" class="btn btn-primary">
                            <span class="glyphicon glyphicon-info-sign"></span>
                        </a>
                        &nbsp;
                        <a href="#" data-toggle="selection" data-state="{{state}}" class="btn btn-{{color}}">
                            <span class="glyphicon glyphicon-{{icon}}"></span>
                        </a>
                    </div>
                </td>
            </tr>
        {{else}}
            <tr>
                <td colspan="5"><?= __('No search results') ?></td>
            </tr>
        {{/each}}
    </script>
</table>

<div class="text-center">
    <ul class="pagination">

    </ul>
</div>

<script id="pagination" type="text/x-handlebars-template">
    <ul class="pagination">
        {{#paginate pagination type="previous"}}
            <li {{#if disabled}}class="disabled"{{/if}} data-page-number="{{n}}">
                <a href="#page-{{n}}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        {{/paginate}}

        {{#paginate pagination type="middle" limit="7"}}
            <li {{#if active}}class="active"{{/if}} data-page-number="{{n}}"><a href="#page-{{n}}">{{n}}</a></li>
        {{/paginate}}

        {{#paginate pagination type="next"}}
            <li {{#if disabled}}class="disabled"{{/if}} data-page-number="{{n}}">
                <a href="#page-{{n}}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        {{/paginate}}
    </ul>
</script>

<div class="text-center">
    <?= $this->Form->button(__('Add own internship'), [
        'class' => 'btn btn-primary position-create-open-modal',
    ]); ?>
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
                        <strong><?= __('Position') ?></strong>: <span class="study-program-description"></span><br/>
                        <strong><?= __('Description') ?></strong>: <br/>
                        <p class="position-description"></p>

                        <address>
                            <strong><?= h(__('Adres')); ?></strong><br/>
                            <span class="company-address-address"></span><br/>
                            <span class="company-address-city"></span> <span class="company-address-postcode"></span>
                        </address>

                        <strong><?= __('E-mail') ?></strong>: <span class="company-email"></span><br/>
                        <strong><?= __('Website') ?></strong>: <a class="company-website" target="_blank"></a><br/>
                        <strong><?= __('Telephone') ?></strong>: <span class="company-telephone"></span>
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
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close') ?></button>
                <button type="button" class="btn btn-success position-select"><?= __('Add') ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade position-create-modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Add internship') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <?= __('Didn\'t find an internship in our list but you\'ve contacted a company on your own? This company properly didn\'t update there details correctly at the internship registry. Because of that, it isn\'t in in our list (yet). Here you\'re able to add an internship for yourself.<br/><br/>Please fill in the 3 fields on the right, after you\'ve filled in all fields it will be added to your selection.') ?>
                    </div>
                    <div class="col-md-8">
                        <?= $this->Form->create(false); ?>
                        <?= $this->Form->input('learning_pathway', [
                            'options' => [
                                'BBL' => 'BBL',
                                'BOL' => 'BOL',
                                'VMBO' => 'VMBO',
                                'HBO' => 'HBO'
                            ],
                            'label' => __('Learning pathway')
                        ]); ?>
                        <?= $this->Form->input('company_id', [
                            'type' => 'autocomplete',
                            'label' => __('Company'),
                            'autocompleteUrl' => ['plugin' => false, 'controller' => 'Companies', 'action' => 'index', '_ext' => 'json', '_method' => 'GET'],
                        ]) ?>
                        <?= $this->Form->input('study_program_id', [
                            'type' => 'autocomplete',
                            'label' => __('Study program'),
                            'autocompleteUrl' => ['plugin' => false, 'controller' => 'StudyPrograms', 'action' => 'index', '_ext' => 'json', '_method' => 'GET'],
                            'autocompleteValue' => 'description'
                        ]) ?>
                        <?= $this->Form->end(); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close') ?></button>
                <button type="button" class="btn btn-success position-create"><?= __('Add') ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
