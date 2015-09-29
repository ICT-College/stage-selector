var requestData = null;
var isFadingOut = true;
var map = null;
var geocoder = null;
var marker = null;

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
    $(document).on('click', '[data-toggle="selection"]', function() {
        // Make sure the button isn't disabled..
        if ($(this).attr('disabled') == 'disabled') {
            return;
        }

        // Update the button's content to a awesome rotating refresh icon and disable it
        $(this).html('<span class="glyphicon glyphicon-refresh spinning"></span>').attr('disabled', 'disabled');

        var self = this;
        setTimeout(function() {
            // Update the button's content to the "next state".
            if ($(self).data('state') == 'add') {
                $(self).html('<span class="glyphicon glyphicon-remove"></span>').removeAttr('disabled').attr('class', 'btn btn-danger pull-right').data('state', 'delete');
            } else {
                $(self).html('<span class="glyphicon glyphicon-plus"></span>').removeAttr('disabled').attr('class', 'btn btn-success pull-right').data('state', 'add');
            }
        }, 1000);
    });

    $(document).on('click', '[data-id]', function (e) {
        if ($(e.target).prop('tagName') == 'A' || $(e.target).prop('tagName') == 'SPAN') {
            return;
        }

        loadModalContent($(this).data('id'));
    });

    // Autocomplete with AJAX call for companies input
    var companyInput = document.getElementById("company-name");
    var companyTimeoutTimer;

    var companyInputAwesomplete = new Awesomplete(companyInput, {
        autoFirst: true
    });

    $(companyInput).on('keyup', function(e) {
        if ([13, 40, 38].indexOf(e.keyCode) != -1) {
            return;
        }

        if (companyTimeoutTimer != null) {
            clearTimeout(companyTimeoutTimer);
        }

        companyInputAwesomplete.list = [];

        companyTimeoutTimer = setTimeout(function() {
            var search = $(companyInput).val();

            $.get('/api/companies.json', { limit: 5, name: search }, function(data) {
                if (data.success) {
                    var results = data.data.map(function(e) {
                        return e.name;
                    });

                    companyInputAwesomplete.list = results;
                } else {
                    companyInputAwesomplete.list = [];
                }
            });
        }, 500);
    });

    $('.position-modal .position-select').on('click', function() {
        var id = $('.position-modal').modal('hide').data('id');

        $('tr[data-id=' + id + '] a[data-toggle="selection"]').click();
    });

    // All tooltips are tooltips, dammit bootstrap!
    $('[data-toggle="tooltip"]').tooltip();

    // Open the filters collapse after 500ms has passed. This gives an awesome effect.
    setTimeout(function() {
        $('#filters').collapse('show');

        loadContent();
    }, 500);

});

function loadContent() {
    //Hide collapse and set spinning icon
    $('#filters').parents('.panel').find('.panel-title .glyphicon').last().remove();
    $('#filters').collapse('hide').parents('.panel').find('.panel-title').append('<span class="glyphicon glyphicon-refresh spinning pull-right"></span>');

    //Receive records and create an object with only usefull filters
    var filters = {};

    $('#filters').find('input[type!="submit"]').each(function() {
        var filter = $(this).attr('name');
        var value = $(this).val();

        if (value != undefined && value != '' && value != 0 && value != null) {
            filters[filter] = value;
        }
    });

    filters['page'] = $('.positions').data('page');

    $.get('/api/positions.json', filters, function(data) {
        $('#filters').parents('.panel').find('.panel-title .glyphicon').last().remove();
        $('.pagination').parent().hide();

        $('.positions > tbody').hide(50, function() {
            $('.positions > tbody > tr').remove();

            data.data.forEach(function (value, key) {
                $('.positions > tbody').append('<tr data-id="' + value.id + '"><th scope="row">' + value.amount + '</th><td>' + value.study_program.description+ '</td><td>' + value.company.name + '<br/>Tel: 0612346578</td><td>' + value.company.address.address + '<br/>' + value.company.address.postcode +  ' ' + value.company.address.city + '</td><td><a href="#toggle-' + value.id + '" data-toggle="selection" data-state="add" class="btn btn-success pull-right"><span class="glyphicon glyphicon-plus"></span></a></td></tr>');
            });

            if (data.data.length == 0) {
                $('.positions > tbody').append('<tr><td colspan="5">Geen zoekresultaten</td></tr>')
            }

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

            $('.pagination li').remove();

            if (number >= 1 && data.data.length != 0) {
                for (var i = 1; i <= 9; i++) {
                    if (number > data.pagination.page_count) {
                        break;
                    }

                    $('.pagination').append('<li class="' + ((currentPage == number) ? 'active' : '') + '"><a href="javascript:;">' + number + '</a></li>');

                    number++;
                }

                $('.pagination').parent().show();
            }

            $('.positions > tbody').show(50);
        });
    });
}

function loadModalContent(id) {
    var modalBody = $('.position-modal');

    $('.loading-modal').modal('show').modal('lock').one('shown.bs.modal', function() {
        $.get('/api/positions/' + id + '.json', function (data) {
            modalBody.data('id', id);

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

                if ((data.data.qualification_parts.length/2) > count) {
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

            modalBody.find('.company-address-address').text(data.data.company.address.address);
            modalBody.find('.company-address-city').text(data.data.company.address.city);
            modalBody.find('.company-address-postcode').text(data.data.company.address.postcode);
            modalBody.find('.company-correspondence-address-address').text(data.data.company.correspondence_address.address);
            modalBody.find('.company-correspondence-address-city').text(data.data.company.correspondence_address.city);
            modalBody.find('.company-correspondence-address-postcode').text(data.data.company.correspondence_address.postcode);
            console.log(data);

            modalBody.find('.company-email').text(data.data.company.email);
            modalBody.find('.company-website').text(data.data.company.website);
            modalBody.find('.company-website').attr('href', 'http://' + data.data.company.website);
            modalBody.find('.company-telephone').text(data.data.company.telephone);

            modalBody.find('iframe').attr('src', 'https://www.google.com/maps/embed/v1/place?q=' + data.data.company.address.address + ' ' + data.data.company.address.postcode + ' ' + data.data.company.correspondence_address.city + '&key=AIzaSyA62DHgWRaIuWaS4CtWAwePExLX_-5j7UI');

            var state = $('tr[data-id=' + id + '] a[data-toggle="selection"]').data('state');

            if (state == 'add') {
                modalBody.find('.position-select').removeClass('btn-danger').addClass('btn-success').text('Add to selection');
            } else {

                modalBody.find('.position-select').addClass('btn-danger').removeClass('btn-success').text('Delete from selection');
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
