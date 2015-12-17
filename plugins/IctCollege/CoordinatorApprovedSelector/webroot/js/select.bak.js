var selected = [];

$(function() {

    // Update icon when toggling the #filters collapse
    $('#filters').on('show.bs.collapse', function() {
        $(this).parents('.panel').find('.panel-title .glyphicon').first().attr('class', 'glyphicon glyphicon-chevron-down');
    });

    $('#filters').on('hide.bs.collapse', function() {
        $(this).parents('.panel').find('.panel-title .glyphicon').first().attr('class', 'glyphicon glyphicon-chevron-up');
    });

    // Open/close a collapse when you click on the header
    $('.panel-heading').on('click', function() {
        $(this).parents('.panel').find('.panel-collapse').collapse('toggle');
    });

    // Catch filter form submit, we won't submit it through a normal GET but awesome AJAX request :-)
    $('#filter').submit(function(e) {
        e.preventDefault();

        $('.positions').data('page', 1);

        loadContent();

        return false;
    });

    $(document).on('click', '.pagination > li > a', function() {
        $('.positions').data('page', $(this).html());

        loadContent();
    });

    // Did someone click a add or remove button? Catch it!
    $(document).on('click', '[data-toggle="selection"]', function(e) {
        // Make sure the button isn't disabled..
        if ($(this).attr('disabled') == 'disabled') {
            return;
        }

        var state = $(this).attr('data-state');

        if (state == 'add') {
            state = 'delete';
        } else {
            state = 'add';
        }

        updatePositionState($(this).closest('[data-position-id]').attr('data-position-id'), state);

        e.stopImmediatePropagation();
    });

    $(document).on('click', '[data-position-id] td:not(:last-child), [data-toggle="modal"]', function (e) {
        loadModalContent($(this).closest('[data-position-id]').data('position-id'));
    });

    $(document).on('click', '.nav-selection [data-position-id]', function (e) {
        loadModalContent($(this).closest('[data-position-id]').data('position-id'));
    });

    $('.position-create-open-modal').click(function () {
        $('.position-create-modal').modal('show');
    });

    $('.position-create').click(function () {
        $.post('/api/positions.json', $('.position-create-modal form').serializeArray(), function (result) {
            console.log(result);
            updatePositionState(result.data.id, 'delete');
        });
    });

    $('.position-modal .position-select').on('click', function() {
        $('.position-modal').modal('hide');

        var id = $(this).closest('[data-position-id]').attr('data-position-id');

        var state = $(this).attr('data-state');

        if (state == 'add') {
            state = 'delete';
        } else {
            state = 'add';
        }

        updatePositionState(id, state);
    });

    $('#radius').slider({
        formatter: function(value) {
            return value + 'km';
        }
    });

    // All tooltips are tooltips, dammit bootstrap!
    $('[data-toggle="tooltip"]').tooltip();

    // Open the filters collapse after 500ms has passed. This gives an awesome effect.
    setTimeout(function() {
        $('#filters').collapse('show');
    }, 500);

    // Startup behaviour
    $('.loading-modal').modal('show').modal('lock');

    updateSelected(true);
});

function updateSelected(load) {
    $.get('/api/coordinator_approved_selector/internship_applications.json', {}, function(data) {
        if (data.success) {
            selected = data.data;

            $('.nav-selection li').remove();
            for (var i = 0; i < 4; i++) {
                var select = selected[i];
                var count = i + 1;
                var side = ((count <= 2) ? 'first' : 'last');

                if (select) {
                    if (!select.accepted_coordinator) {
                        $('.nav-selection:' + side).append('<li data-position-id="' + select.position.id + '" class="active"><a href="#">' + count + '. ' + select.position.company.name +  ' - ' + select.position.study_program.description + ' <button type="button" class="close" data-toggle="selection" aria-label="Close"><span aria-hidden="true">×</span></button></a></li>');
                    } else {
                        $('.nav-selection:' + side).append('<li class="disabled"><a href="#">' + count + '. ' + select.position.company.name +  ' - ' + select.position.study_program.description + ' </a></li>');
                    }
                } else {
                    $('.nav-selection:' + side).append('<li><a href="#">' + count + '.</a></li>');
                }
            }

            if (selected.length >= 4) {
                $('[data-state="add"]').attr('disabled', 'disabled');
            } else {
                $('[data-state="add"]').removeAttr('disabled');
            }

            if (load) {
                loadContent();
            }
        }
    });
}

function updatePositionState(id, state) {
    if (state == 'add') {
        var oldState = 'delete';
    } else {
        var oldState = 'add';
    }

    // Update the button's content to a awesome rotating refresh icon and disable it
    $('[data-position-id=' + id + '] a[data-toggle="selection"]').html('<span class="glyphicon glyphicon-refresh spinning"></span>').attr('disabled', 'disabled').attr('data-state', 'load');

    $.ajax({
        'url': '/api/coordinator_approved_selector/internship_applications' + ((oldState == 'add') ? '' : '/position-delete') + '.json',
        'method': (oldState == 'add') ? 'POST' : 'DELETE',
        'data': {
            'position_id': id
        },
        'success': function(data) {
            if (data.success) {
                updateSelected(false);

                if (state == 'add') {
                    $('[data-position-id=' + id + '] a[data-toggle="selection"]').attr('data-state', 'add').html('<span class="glyphicon glyphicon-plus"></span>').removeAttr('disabled').attr('class', 'btn btn-success');
                } else {
                    $('[data-position-id=' + id + '] a[data-toggle="selection"]').attr('data-state', 'delete').html('<span class="glyphicon glyphicon-remove"></span>').removeAttr('disabled').attr('class', 'btn btn-danger');
                }
            }
        },
        'error': function() {
            $('[data-position-id=' + id + '] [data-toggle="selection"]').html('<span class="glyphicon glyphicon-question-sign"></span>').attr('class', 'btn btn-default').data('state', 'error');
        }
    });
}

function loadContent() {
    //Hide collapse and set spinning icon
    $('#filters').parents('.panel').find('.panel-title .glyphicon .spinning').last().remove();
    $('#filters').parents('.panel').find('.panel-title').append('<span class="glyphicon glyphicon-refresh spinning pull-right"></span>');

    //Receive records and create an object with only usefull filters
    var filters = {};

    $('#filters').find('input[type!="submit"], select').each(function() {
        var filter = $(this).attr('name');
        var value = $(this).val();

        if (value != undefined && value != '' && value != 0 && value != null) {
            filters[filter] = value;
        }
    });

    if (filters['study_program_id']) {
        filters['study_program_id'] = filters['study_program_id'].split('-')[0].replace(/\D/g,'');
    }

    if (!filters['company_address'] && !filters['company_postcode'] && !filters['company_city']) {
        delete filters['radius'];
    }

    filters['page'] = $('.positions').data('page');

    $('.loading-modal').modal('show').modal('lock').one('shown.bs.modal', function() {
        $.get('/api/positions.json', filters, function (data) {
            $('#filters').parents('.panel').find('.panel-title .glyphicon').last().remove();
            $('.pagination').parent().hide();

            $('.positions > tbody').hide(50, function () {
                $('.positions > tbody > tr').remove();

                var positions = [];

                data.data.forEach(function (value, key) {
                    value.state = 'add';
                    value.color = 'success';
                    value.icon = 'plus';

                    selected.forEach(function (selectValue, selectKey) {
                        if (selectValue.position.id == value.id) {
                            if (selectValue.accepted_coordinator) {
                                value.state = 'accepted';
                                value.color = 'default disabled';
                                value.icon = 'ok';
                            } else {
                                value.state = 'delete';
                                value.color = 'danger';
                                value.icon = 'remove';
                            }
                        }
                    });

                    positions.push(value);
                });

                var template = Handlebars.compile($('#positions').html());

                $('.positions > tbody').html(template({
                    positions: positions
                }));

                var currentPage = data.pagination.current_page;
                var number = data.pagination.current_page - 4;
                var lastSetPage = (data.pagination.page_count - 3);
                if (number < 1) {
                    number = 1;
                }
                if (lastSetPage < 1) {
                    lastSetPage = 1;
                }
                if (currentPage <= data.pagination.page_count && currentPage >= lastSetPage) {
                    number = currentPage - (8 - (data.pagination.page_count - currentPage));
                }
                if (number < 1) {
                    number = 1;
                }

                $('.pagination li').remove();

                if (data.pagination.page_count > 1 && data.data.length != 0) {
                    for (var i = 1; i <= 9; i++) {
                        if (number > data.pagination.page_count) {
                            break;
                        }

                        $('.pagination').append('<li class="' + ((currentPage == number) ? 'active' : '') + '"><a href="javascript:;">' + number + '</a></li>');

                        number++;
                    }

                    $('.pagination').parent().show();
                }

                if (selected.length == 4) {
                    $('[data-state="add"]').attr('disabled', 'disabled');
                } else {
                    $('[data-state="add"]').removeAttr('disabled');
                }

                $('.loading-modal').modal('unlock').modal('hide');

                $('.positions > tbody').show(50);
            });
        });
    });
}

function loadModalContent(id) {
    var modalBody = $('.position-modal');

    $('.loading-modal').modal('show').modal('lock').one('shown.bs.modal', function() {
        $.get('/api/positions/' + id + '.json', function (data) {
            modalBody.attr('data-position-id', id);

            modalBody.find('.study-program-title').text(data.data.study_program.description + ' at ' + data.data.company.name);
            modalBody.find('.study-program-description').text(data.data.study_program.description);
            modalBody.find('.company-name').text(data.data.company.name);
            modalBody.find('.position-description').text(((data.data.description == '') ? 'No description' : data.data.description));

            modalBody.find('.qualification-parts').html('');
            modalBody.find('.qualification-parts').last().attr('start', 0);
            var count = 1;

            for (var qualificationPartIndex in data.data.qualification_parts) {
                var qualificationPart = data.data.qualification_parts[qualificationPartIndex];

                var listItem = $('<li>', {
                    'text': qualificationPart.description + '.'
                });

                if ((data.data.qualification_parts.length/2) >= count) {
                    console.log('links');
                    modalBody.find('.qualification-parts').first().append(listItem);
                } else {
                    console.log('rechts');
                    if (modalBody.find('.qualification-parts').last().attr('start') == 0) {
                        modalBody.find('.qualification-parts').last().attr('start', count);
                    }

                    modalBody.find('.qualification-parts').last().append(listItem)
                }

                count++;
            }

            modalBody.find('.company-address-address').text(data.data.company.address);
            modalBody.find('.company-address-postcode').text(data.data.company.city);
            modalBody.find('.company-address-city').text(data.data.company.postcode);
            //modalBody.find('.company-correspondence-address-address').text(data.data.company.address);
            //modalBody.find('.company-correspondence-address-city').text(data.data.company.city);
            //modalBody.find('.company-correspondence-address-postcode').text(data.data.company.postcode);

            modalBody.find('.company-email').text(data.data.company.email);
            modalBody.find('.company-website').text(data.data.company.website);
            modalBody.find('.company-website').attr('href', data.data.company.website);
            modalBody.find('.company-telephone').text(data.data.company.telephone);

            modalBody.find('iframe').attr('src', 'https://www.google.com/maps/embed/v1/place?q=' + data.data.company.address + ' ' + data.data.company.postcode + ' ' + data.data.company.city + '&key=AIzaSyA62DHgWRaIuWaS4CtWAwePExLX_-5j7UI');

            var state = 'add';

            selected.forEach(function (value, key) {
                if (value.position.id == id) {
                    state = 'delete';
                }
            });

            if (state == 'add') {
                modalBody.find('.position-select').attr('data-state', 'add').removeClass('btn-danger').addClass('btn-success').text('Add to selection');

                if (selected.length >= 4) {
                    modalBody.find('.position-select').attr('disabled', 'disabled');
                }
            } else {
                modalBody.find('.position-select').attr('data-state', 'delete').addClass('btn-danger').removeClass('btn-success').text('Delete from selection');
            }

            if (selected.length < 4) {
                modalBody.find('.position-select').removeAttr('disabled');
            }

            $('.loading-modal').modal('unlock').modal('hide').one('hidden.bs.modal', function() {
                $('.position-modal').modal('show').one('shown.bs.modal', function() {
                    modalBody.find('iframe').height(modalBody.find('.col-md-6').first().height());
                });
            });
        });
    });
}

/**
 * Adds a parameter to a given URL
 *
 * Copyright to that awesome guy from Stackoverflow: http://stackoverflow.com/a/10997390/2391566
 *
 * @param url URL to be modified
 * @param param Param key to be added
 * @param paramVal Param value to be added
 * @returns baseuRL The modified base url
 */
function setParameter (url, param, paramVal){
    var parts = url.split('?');
    var baseUrl = parts[0];
    var oldQueryString = parts[1];
    var newParameters = [];
    if (oldQueryString) {
        var oldParameters = oldQueryString.split('&');
        for (var i = 0; i < oldParameters.length; i++) {
            if(oldParameters[i].split('=')[0] != param) {
                newParameters.push(oldParameters[i]);
            }
        }
    }
    if (paramVal !== '' && paramVal !== null && typeof paramVal !== 'undefined') {
        newParameters.push(param + '=' + encodeURI(paramVal));
    }
    if (newParameters.length > 0) {
        return baseUrl + '?' + newParameters.join('&');
    } else {
        return baseUrl;
    }
}

/**
 * Add ability to lock a modal
 */
var _hide = $.fn.modal.Constructor.prototype.hide;

$.extend($.fn.modal.Constructor.prototype, {
    lock: function() {
        this.options.locked = true;
    },
    unlock: function() {
        this.options.locked = false;
    },
    hide: function() {
        if (this.options.locked) return;

        _hide.apply(this, arguments);
    }
});
