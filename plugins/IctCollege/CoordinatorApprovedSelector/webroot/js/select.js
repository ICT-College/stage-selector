/**
 * Select array wich holds all classes.
 *
 * @constructor
 */
var select = [];

/**
 * Loader class which controls the loading modal
 *
 * @type {{isLoading: boolean, start: Function, end: Function}}
 */
select.Loader = {
    /**
     * Loader variables
     */
    isLoading: false,

    /**
     * Loader methods
     */
    start: function() {
        if (this.isLoading) {
            return true;
        }

        $('.loading-modal').modal('show').modal('lock');
    },

    stop: function() {
        $('.loading-modal').modal('unlock').modal('hide');

        this.isLoading = false;
    }
};

/**
 * Request class which handles all requests for us
 *
 * @type {{inRequest: boolean, get: Function, post: Function, request: Function}}
 */
select.Request = {
    /**
     * Request variables
     */
    inRequest: false,

    /**
     * Request methods
     */
    get: function(uri, data, callback, loader) {
        this.request('GET', uri, data, callback, loader);
    },

    post: function(uri, data, callback, loader) {
        this.request('POST', uri, data, callback, loader);
    },

    request: function(method, uri, data, callback, loader) {
        // Loader is by default true
        if (typeof loader == 'undefined') {
            loader = true;
        }

        if (loader) {
            select.Loader.start();
        }

        $.ajax({
            method: method,
            url: uri,
            data: data
        }).done(function(response) {
            callback(true, response);
        }).fail(function(jqXHR, textStatus) {
            callback(false, response, textStatus);
        }).always(function() {
            if (loader) {
                select.Loader.stop();
            }
        });
    }
};

select.Selection = {
    /**
     * Selection variables
     */
    current: [ ],

    /**
     * Selection methods
     */
    refresh: function() {
        select.Request.get('/api/coordinator_approved_selector/internship_applications.json', {}, function(success, data) {
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
    },

    add: function(id) {

        this.refresh();
    },

    remove: function(id) {

        this.refresh();
    }
};

/**
 * Initialize method for starting the page
 */
select.initialize = function() {

    select.Selection.refresh();
};

/**
 * Initialize when DOM is ready
 */
$(function() {
    select.initialize();
});

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
