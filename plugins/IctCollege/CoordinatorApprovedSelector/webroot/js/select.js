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

/**
 * Selections class which handles everything to do with the selections.
 *
 * @type {{current: Array, refresh: Function, add: Function, remove: Function}}
 */
select.Selection = {
    /**
     * Selection variables
     */
    current: [ ], // Holds the current set of selections

    /**
     * Selection methods
     */
    initialize: function() {
        select.Selection.refresh();
    },

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

                    var template = Handlebars.compile($('#selection').html());

                    $('.selection').html(template({
                        selection: select.Selection.current
                    }));
                }

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
 * Positions class which handles everything to do with positions
 *
 * @type {{load: Function}}
 */
select.Positions = {

    /**
     * Positions methods
     */
    initialize: function() {
        select.Positions.load();
    },

    load: function() {
        select.Loader.start(function() {
            var filters = select.Filters.get();

            select.Request.get('/api/positions.json', filters, function(success, data) {
                if (success) {

                    $('.positions > tbody').hide(50, function () {
                        $('.positions > tbody > tr').remove();

                        var positions = [];

                        data.data.forEach(function (value, key) {
                            value.state = 'add';
                            value.color = 'success';
                            value.icon = 'plus';

                            select.Selection.current.forEach(function (selectValue, selectKey) {
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

                        var template = Handlebars.compile($('#pagination').html());

                        $('.pagination').parent().html(template({pagination: {
                            page: data.pagination.current_page,
                            pageCount: data.pagination.page_count
                        }}));

                        if (select.Selection.current.length == 4) {
                            $('[data-state="add"]').attr('disabled', 'disabled');
                        } else {
                            $('[data-state="add"]').removeAttr('disabled');
                        }

                        $('.positions > tbody').show(50);

                        select.Loader.stop();
                    });
                }
            });
        });
    }
};

/**
 * Filters class which handles mostly things to do with the filters
 *
 * @type {{initialize: Function}}
 */
select.Filters = {

    /**
     * Positions variables
     */
    page: 1,

    /**
     * Filters methods
     */
    initialize: function() {
        select.Filters.bind();

        // Initialize slider
        $('#radius').slider({
            formatter: function(value) {
                return value + 'km';
            }
        });

        // Collapse the filters to show
        $('#filters').collapse('show');
    },

    bind: function() {
        $('#filters')
            // Update arrow when collapses
            .on('show.bs.collapse', function() {
                $(this).parents('.panel').find('.panel-title .glyphicon').first().attr('class', 'glyphicon glyphicon-chevron-down');
            })
            .on('hide.bs.collapse', function() {
                $(this).parents('.panel').find('.panel-title .glyphicon').first().attr('class', 'glyphicon glyphicon-chevron-up');
            })
            // Catch filter form submit, we won't submit it using a default GET but through a fancy AJAX request.
            .on('submit', function(e) {
                e.preventDefault();

                select.Filters.page = 1;

                select.Positions.load();

                return false;
            });

        // Open/close a collapse when you click on the header
        $('.panel-heading').on('click', function() {
            $(this).parents('.panel').find('.panel-collapse').collapse('toggle');
        });
    },

    get: function() {
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

        return filters;
    }
};

/**
 * Initialize method for starting the page
 */
select.initialize = function() {
    // All tooltips are tooltips
    $('[data-toggle="tooltip"]').tooltip();

    select.Filters.initialize();
    select.Selection.initialize();
    select.Positions.initialize();
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
 * Add to helper to Handlebars.
 *
 * This helper will count to the first defined int
 * and it will pass the same index from the context.
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

/**
 * Add modulo helper to Handlebars.
 *
 * This helper will do then modulo expression
 * and it will return true or false.
 */
Handlebars.registerHelper('modulo', function(from, number, match) {
    if (from % number === match) {
        return true;
    } else {
        return false;
    }
});

/**
 * Add paginate helper to Handlebars.
 *
 * Thanks to https://github.com/olalonde/handlebars-paginate for creating
 * this awesome pagination helper.
 */
Handlebars.registerHelper('paginate', function(pagination, options) {
    var type = options.hash.type || 'middle';
    var ret = '';
    var pageCount = Number(pagination.pageCount);
    var page = Number(pagination.page);
    var limit;
    if (options.hash.limit) limit = +options.hash.limit;

    //page pageCount
    var newContext = {};
    switch (type) {
        case 'middle':
            if (typeof limit === 'number') {
                var i = 0;
                var leftCount = Math.ceil(limit / 2) - 1;
                var rightCount = limit - leftCount - 1;
                if (page + rightCount > pageCount)
                    leftCount = limit - (pageCount - page) - 1;
                if (page - leftCount < 1)
                    leftCount = page - 1;
                var start = page - leftCount;

                while (i < limit && i < pageCount) {
                    newContext = { n: start };
                    if (start === page) newContext.active = true;
                    ret = ret + options.fn(newContext);
                    start++;
                    i++;
                }
            }
            else {
                for (var i = 1; i <= pageCount; i++) {
                    newContext = { n: i };
                    if (i === page) newContext.active = true;
                    ret = ret + options.fn(newContext);
                }
            }
            break;
        case 'previous':
            if (page === 1) {
                newContext = { disabled: true, n: 1 }
            }
            else {
                newContext = { n: page - 1 }
            }
            ret = ret + options.fn(newContext);
            break;
        case 'next':
            newContext = {};
            if (page === pageCount) {
                newContext = { disabled: true, n: pageCount }
            }
            else {
                newContext = { n: page + 1 }
            }
            ret = ret + options.fn(newContext);
            break;
        case 'first':
            if (page === 1) {
                newContext = { disabled: true, n: 1 }
            }
            else {
                newContext = { n: 1 }
            }
            ret = ret + options.fn(newContext);
            break;
        case 'last':
            if (page === pageCount) {
                newContext = { disabled: true, n: pageCount }
            }
            else {
                newContext = { n: pageCount }
            }
            ret = ret + options.fn(newContext);
            break;
    }

    return ret;
});
