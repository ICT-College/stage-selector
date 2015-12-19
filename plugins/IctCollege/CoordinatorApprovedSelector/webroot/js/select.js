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
    loadings: 0, // Holds how many times we're loading something.

    /**
     * Loader methods
     */
    start: function(callback) {
        if (select.Loader.loadings > 0) { // If it's higher than zero, we don't want the modal to be shown again.
            select.Loader.loadings++;

            if (typeof callback != 'undefined') {
                callback();
            }

            return;
        }

        select.Loader.loadings++;

        $('.loading-modal').modal('show').modal('lock').one('shown.bs.modal', function() {
            if (typeof callback != 'undefined') {
                callback();
            }
        });
    },

    stop: function(callback) {
        if (select.Loader.loadings > 1) { // if it's higher than one, we don't want the modal to be hidden yet.
            select.Loader.loadings--;

            if (typeof callback != 'undefined') {
                callback();
            }

            return;
        }

        $('.loading-modal').modal('unlock').modal('hide').one('shown.bs.modal', function() {
            select.Loader.loadings--;

            if (typeof callback != 'undefined') {
                callback();
            }
        });
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
    get: function(uri, data, callback) {
        this.request('GET', uri, data, callback);
    },

    post: function(uri, data, callback) {
        this.request('POST', uri, data, callback);
    },

    request: function(method, uri, data, callback) {
        $.ajax({
            method: method,
            url: uri,
            data: data
        }).done(function(response) {
            callback(true, response);
        }).fail(function(jqXHR, textStatus) {
            callback(false, response, textStatus);
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
        select.Loader.start(function() {
            select.Request.get('/api/coordinator_approved_selector/internship_applications.json', {}, function(success, data) {
                if (success && data.success) {
                    select.Selection.current = data.data;

                    if (select.Selection.current.length >= 4) {
                        $('[data-state="add"]').attr('disabled', 'disabled');
                    } else {
                        $('[data-state="add"]').removeAttr('disabled');
                    }
                } else {

                }

                var template = Handlebars.compile($('#selection').html());

                $('.selection').html(template({
                    selection: select.Selection.current
                }));

                select.Loader.stop();
            });
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

/**
 * Add to helper to Handlebars,
 * this helper will count to the first defined int
 * and it will pass the same index from the context
 */
Handlebars.registerHelper('to', function(to, context, options) {
    var ret = "";

    for(var i=0; i < to; i++) {
        var data = context[i];

        if (typeof data == 'undefined') {
            data = {exists: false};
        } else {
            data.exists = true;
        }

        data.index = i;
        data.first = i === 0;
        data.last = i === (to - 1);
        data.count = context.length;
        data.current = i + 1;

        ret = ret + options.fn(data);
    }

    return ret;
});

Handlebars.registerHelper('modulo', function(from, number, match) {
    if (from % number === match) {
        return true;
    } else {
        return false;
    }
});
