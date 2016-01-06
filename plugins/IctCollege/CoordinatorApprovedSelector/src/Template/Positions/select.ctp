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
                {{#if (side 'open' 4 index)}}
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
                                <button type="button" class="close" data-toggle="selection" data-state="delete" aria-label="Close"><span aria-hidden="true">×</span></button>
                            </a>
                        </li>
                    {{/if}}
                {{else}}
                    <li><a href="#">{{current}}.</a></li>
                {{/if}}

                {{#if (side 'close' 4 index)}}
                        </ul>
                    </div>
                {{/if}}
            {{/to}}
        </script>

        <div class="col-md-1">
            <?= $this->Form->button('<span class="glyphicon glyphicon-chevron-right"></span>', [
                'type' => 'submit',
                'escape' => false,
                'class' => 'btn btn-success pull-right',
                'data-toggle' => 'continue',
                'style' => 'height: 50px;'
            ]) ?>
        </div>
    </div>
</div>
<?= $this->end() ?>

<div class="panel panel-default">
    <div class="panel-heading" role="tab" id="footersHeading">
        <h4 class="panel-title">
            <?= __('Filters'); ?>
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
                    <?= $this->Form->input('company_address', ['label' => __('Address')]) ?>
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
                        'value' => $loggedUser['learning_pathway'],
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
                <a href="#page-{{n}}" onclick="select.Positions.load({{n}});" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        {{/paginate}}

        {{#paginate pagination type="middle" limit="7"}}
            <li {{#if active}}class="active"{{/if}}><a href="#page-{{n}}" onclick="select.Positions.load({{n}});">{{n}}</a></li>
        {{/paginate}}

        {{#paginate pagination type="next"}}
            <li {{#if disabled}}class="disabled"{{/if}} data-page-number="{{n}}">
                <a href="#page-{{n}}" onclick="select.Positions.load({{n}});" aria-label="Next">
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

</div>

<script id="position-modal" type="text/x-handlebars-template">
    <div class="modal-dialog modal-lg" data-position-id="{{details.id}}">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{details.study_program.description}} <?= __('at'); ?> {{details.company.name}}</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong><?= __('Position') ?></strong>: {{details.study_program.description}}<br/>
                        <strong><?= __('Description') ?></strong>: <br/>
                        {{#if details.description}}
                            <p class="position-description">{{details.description}}</p>
                        {{else}}
                            <i><?= __('No description available for this position.'); ?></i><br/><br/>
                        {{/if}}

                        <address>
                            <strong><?= h(__('Adres')); ?></strong><br/>
                            {{details.company.address}}<br/>
                            {{details.company.city}} {{details.company.postcode}}
                        </address>

                        <strong><?= __('E-mail') ?></strong>: {{details.company.email}}<br/>
                        <strong><?= __('Website') ?></strong>: <a target="_blank">{{details.company.website}}</a><br/>
                        <strong><?= __('Telephone') ?></strong>: {{details.company.telephone}}
                    </div>
                    <div class="col-md-6">
                        <iframe style="width: 100%;" src="https://www.google.com/maps/embed/v1/place?q={{details.company.address}} {{details.company.postcode}} {{details.company.city}}&key=AIzaSyA62DHgWRaIuWaS4CtWAwePExLX_-5j7UI"></iframe>
                    </div>
                </div>
                <div class="row">
                    {{#each details.qualification_parts as |qualification_part current|}}
                        {{#if (side 'open' ../details.qualification_parts.length current)}}
                            <div class="col-md-6">
                                <ol class="qualification-parts">
                        {{/if}}

                        <li value="{{qualification_part.number}}">{{qualification_part.description}}</li>

                        {{#if (side 'close' ../details.qualification_parts.length current)}}
                                </ol>
                            </div>
                        {{/if}}
                    {{/each}}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close') ?></button>
                <button type="button" class="btn btn-{{#if selected}}danger{{else}}success{{/if}} position-select" data-state="{{#if selected}}delete{{else}}add{{/if}}">{{#if selected}}<?= __('Remove') ?>{{else}}<?= __('Add') ?>{{/if}}</button>
            </div>
        </div>
    </div>
</script>

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
                            'value' => $loggedUser['learning_pathway'],
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

<div class="modal fade continue-success">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Successfully saved') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= __('We\'ve saved your internships, they will be reviewed by the coordinator and you\'ll receive an email when you\'re able to continue.'); ?>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal fade continue-error">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?= __('Error while saving') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?= __('You haven\'t selected 4 internships yet. Please select 4 internships before continuing.'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?= __('Close') ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
