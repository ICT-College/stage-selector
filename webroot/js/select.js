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

        //Hide and set spinning icon
        $('#filters').collapse('hide').parents('.panel').find('.panel-title').append('<span class="glyphicon glyphicon-refresh spinning pull-right"></span>');

        //Receive records..
        console.log($(this).serialize());

        setTimeout(function() {
            //Remove spinning icon
            $('#filters').parents('.panel').find('.panel-title .glyphicon').last().remove();
        }, 1000);

        return false;
    });

    // Did someone click a add or remove button? Catch it!
    $('[data-toggle="selection"]').on('click', function() {
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

    // All tooltips are tooltips, dammit bootstrap!
    $('[data-toggle="tooltip"]').tooltip();

    // Open the filters collapse after 500ms has passed. This gives an awesome effect.
    setTimeout(function() {
        $('#filters').collapse('show');
    }, 500);

});

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
