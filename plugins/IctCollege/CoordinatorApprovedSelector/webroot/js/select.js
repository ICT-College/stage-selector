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

            // Maybe we should queue all callbacks for when the loading modal is really closed.
            if (typeof callback != 'undefined') {
                callback();
            }

            return;
        }

        $('.loading-modal').modal('unlock').modal('hide').one('hidden.bs.modal', function() {
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
        if (typeof data == 'function') {
            callback = data;
            data = {};
        }

        $.ajax({
            method: method,
            url: uri,
            data: data
        }).done(function(response) {
            callback(true, response);
        }).fail(function(jqXHR, textStatus) {
            if (jqXHR.status == 403) {
                location.reload(true);
                return;
            }

            callback(false, textStatus);
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
    isLoading: false, // Holds the current state of the loader

    /**
     * Selection methods
     */
    initialize: function() {
        select.Selection.bind();

        select.Selection.refresh(true);
    },

    bind: function() {
        $(document)
            .on('click', '[data-toggle="selection"]', function(e) {
                // Make sure the button isn't disabled..
                if ($(this).attr('disabled') == 'disabled') {
                    return;
                }

                var id = $(this).closest('[data-position-id]').attr('data-position-id');

                var state = $(this).attr('data-state');

                if (state == 'add') {
                    select.Selection.add(id);
                } else if (state =='delete') {
                    select.Selection.remove(id);
                } else {

                }

                e.stopImmediatePropagation();
            })
            .on('click', '.position-modal .position-select', function() {
                $('.position-modal').modal('hide');
                console.log('hide');
                var id = $(this).closest('[data-position-id]').attr('data-position-id');

                var state = $(this).attr('data-state');
                console.log(id);
                if (state == 'add') {
                    select.Selection.add(id);
                } else if (state =='delete') {
                    select.Selection.remove(id);
                } else {

                }
            });
    },

    refresh: function(initialize, silence) {
        var doRefresh = function() {
            select.Request.get('/api/coordinator_approved_selector/internship_applications.json', {}, function(success, response) {
                if (success && response.success) {
                    select.Selection.current = response.data;

                    if (select.Selection.current.length >= 4) {
                        $('[data-state="add"]').attr('disabled', 'disabled');
                    } else {
                        $('[data-state="add"]').removeAttr('disabled');
                    }

                    var template = Handlebars.compile($('#selection').html());

                    $('.selection').html(template({
                        selection: select.Selection.current
                    }));

                    if (typeof initialize != 'undefined' && initialize) {
                        select.Positions.initialize();
                    }
                }

                if (typeof silence == 'unknown' || !silence) {
                    select.Loader.stop();
                } else {
                    select.Selection.stopLoading();
                }
            });
        };

        if (typeof silence == 'unknown' || !silence) {
            select.Loader.start(function () {
                doRefresh();
            });
        } else {
            select.Selection.startLoading(function() {
                doRefresh();
            });
        }
    },

    add: function(id) {
        // Update the button's content to a awesome rotating refresh icon and disable it
        $('[data-position-id=' + id + '] a[data-toggle="selection"]').html('<span class="glyphicon glyphicon-refresh spinning"></span>').attr('disabled', 'disabled').attr('data-state', 'load');

        select.Selection.startLoading(function() {
            select.Request.request('POST', '/api/coordinator_approved_selector/internship_applications.json', {
                'position_id': id
            }, function(success, response) {
                if (success && response.success) {
                    select.Selection.refresh(false, true);

                    $('[data-position-id=' + id + '] a[data-toggle="selection"]').attr('data-state', 'delete').html('<span class="glyphicon glyphicon-remove"></span>').removeAttr('disabled').attr('class', 'btn btn-danger');
                } else {
                    $('[data-position-id=' + id + '] a[data-toggle="selection"]').html('<span class="glyphicon glyphicon-question-sign"></span>').attr('class', 'btn btn-default').attr('data-state', 'error');
                }
            });
        });
    },

    remove: function(id) {
        // Update the button's content to a awesome rotating refresh icon and disable it
        $('[data-position-id=' + id + '] a[data-toggle="selection"]').html('<span class="glyphicon glyphicon-refresh spinning"></span>').attr('disabled', 'disabled').attr('data-state', 'load');

        select.Selection.startLoading(function() {
            select.Request.request('DELETE', '/api/coordinator_approved_selector/internship_applications/position-delete.json', {
                'position_id': id
            }, function(success, response) {
                if (success && response.success) {
                    select.Selection.refresh(false, true);

                    $('[data-position-id=' + id + '] a[data-toggle="selection"]').attr('data-state', 'add').html('<span class="glyphicon glyphicon-plus"></span>').removeAttr('disabled').attr('class', 'btn btn-success');
                } else {
                    $('[data-position-id=' + id + '] a[data-toggle="selection"]').html('<span class="glyphicon glyphicon-question-sign"></span>').attr('class', 'btn btn-default').attr('data-state', 'error');
                }
            });
        });
    },

    startLoading: function(callback) {
        if (select.Selection.isLoading) {

            if (typeof callback != 'undefined') {
                callback();
            }

            return;
        }

        select.Selection.isLoading = true;

        $('.selection .glyphicon-refresh.spinning').show();
        $('.selection').fadeTo('slow', 0.5, function() {
            if (typeof callback != 'undefined') {
                callback();
            }
        });
    },

    stopLoading: function(callback) {
        if (!select.Selection.isLoading) {

            if (typeof callback != 'undefined') {
                callback();
            }

            return;
        }

        $('.selection').fadeTo('slow', 1, function() {
            $('.selection .glyphicon-refresh.spinning').hide();

            if (typeof callback != 'undefined') {
                callback();
            }

            select.Selection.isLoading = false;
        });
    }
};

/**
 * Positions class which handles everything to do with positions
 *
 * @type {{load: Function}}
 */
select.Positions = {

    /**
     * Positions variables
     */
    current: {}, // Current set of loaded positions

    /**
     * Positions methods
     */
    initialize: function() {
        select.Positions.load();
        select.Positions.bind();
    },

    bind: function() {
        $('.position-create-open-modal').click(function () {
            $('.position-create-modal').modal('show');
        });

        $('.position-create').click(function () {
            select.Positions.create();
        });

        $('[data-toggle="continue"]').click(function() {
            select.Positions.continue();
        });
    },

    load: function(page) {
        if (typeof page != 'undefined') {
            select.Filters.page = page;
        }

        select.Loader.start(function() {
            var filters = select.Filters.get();

            select.Request.get('/api/positions.json', filters, function(success, response) {
                if (success && response.success) {
                    select.Positions.current = response.data;

                    var positions = [];

                    response.data.forEach(function (value, key) {
                        value.state = 0; // 0 = add, 1 = disabled add, 2 = remove, 3 = accepted

                        select.Selection.current.forEach(function (selectValue, selectKey) {
                            if (selectValue.position.id == value.id) {
                                if (selectValue.accepted_coordinator) {
                                    value.state = 3;
                                } else {
                                    value.state = 2;
                                }
                            }
                        });


                        if (value.state == 0 && select.Selection.current.length >= 4) {
                            value.state = 1;
                        }

                        positions.push(value);
                    });

                    var template = Handlebars.compile($('#positions').html());

                    $('.positions > tbody').html(template({
                        positions: positions
                    }));

                    if (positions.length != 0) {
                        var template = Handlebars.compile($('#pagination').html());

                        $('.pagination').parent().css('display', '').html(template({
                            pagination: {
                                page: response.pagination.current_page,
                                pageCount: response.pagination.page_count
                            }
                        }));
                    } else {
                        $('.pagination').parent().css('display', 'none');
                    }
                }

                select.Loader.stop();
            });
        });
    },

    create: function() {
        $('.position-create-modal').modal('hide').one('hidden.bs.modal', function () {
            select.Loader.start(function() {
                var position = {};

                $('input, select', '.position-create-modal form').each(function() {
                    position[$(this).attr('name')] = $(this).val();
                });

                select.Request.request('POST', '/api/coordinator_approved_selector/internship_applications.json', { position: position }, function(success, response) {
                    if (success && response.success) {
                        select.Selection.refresh();

                        select.Loader.stop();
                    } else {
                        select.Loader.stop(function() {
                            $('.position-create-modal').modal('show');
                        });
                    }
                });
            });
        });
    },

    continue: function() {
        if (select.Selection.current.length < 4) {
            $('.continue-error').modal('show');
            return;
        }

        select.Loader.start(function() {
            select.Request.post('/api/coordinator_approved_selector/internship_applications/submit.json', {}, function(success, response) { // Request to set flash and send e-mail
                if (success) { // success && response.success
                    window.location = '/coordinator-approved-selector/internship-applications';
                } else {
                    select.Loader.stop(function() {
                        $('.continue-error').modal('show');
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

        filters['page'] = select.Filters.page;

        return filters;
    }
};

/**
 * Details class which handles all the things to do with the details modal
 *
 * @type {{initialize: Function, bind: Function, load: Function}}
 */
select.Details = {

    initialize: function() {
        select.Details.bind();
    },

    bind: function() {
        $(document)
            // Click handlers to open the details box
            .on('click', '[data-position-id] td:not(:last-child), [data-toggle="modal"]', function (e) {
                var id = $(this).closest('[data-position-id]').data('position-id');

                select.Details.load(id);
            })
            .on('click', '.nav-selection [data-position-id]', function (e) {
                var id = $(this).closest('[data-position-id]').data('position-id');

                select.Details.load(id);
            });
    },

    load: function(id) {
        select.Loader.start(function() {
            select.Request.get('/api/positions/' + id + '.json', function(success, response) {
                if (success && response.success) {

                    var state = 0; // 0 = add, 1 = disabled add, 2 = remove, 3 = accepted

                    select.Selection.current.forEach(function (value, key) {
                        if (value.position.id == id) {
                            if (value.accepted_coordinator) {
                                state = 3;
                            } else {
                                state = 2;
                            }
                        }
                    });

                    if (state == 0 && select.Selection.current.length >= 4) {
                        state = 1;
                    }

                    var template = Handlebars.compile($('#position-modal').html());

                    $('.position-modal').html(template({
                        details: response.data,
                        state: state
                    }));

                    select.Loader.stop(function() {
                        $('.position-modal').modal('show').one('shown.bs.modal', function() {
                            $('.position-modal').find('iframe').height($('.position-modal').find('.col-md-6').first().height());
                        });
                    });
                } else {
                    select.Loader.stop();
                }
            });
        });
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
    // select.Positions.initialize() done in the selection initialize
    select.Details.initialize();
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
 * Add a ifSame helper to Handlebars.
 *
 * This helper will do a check if 2 variables are the same.
 */
Handlebars.registerHelper('ifSame', function(v1, v2, options) {
    if(v1 === v2) {
        return options.fn(this);
    }
    return options.inverse(this);
});

/**
 * Add side helper to Handlebars.
 *
 * This helper will calculate which side and what to happen
 * and it will return true or false when something needs to happen.
 */
Handlebars.registerHelper('side', function(state, totalItems, currentItem, sides) {
    if (typeof sides == 'object' || typeof sides == 'undefined') {
        sides = 2;
    }

    var itemsPerSlide = Math.ceil(totalItems / sides);

    var array = [];

    for (var i = 0; i<= totalItems; i++) {
        array.push(i * itemsPerSlide);
    }

    if (state === 'open') {
        if (currentItem == 0) {
            return true;
        }

        var isFirst = false;

        array.forEach(function(item, index) {
            if (currentItem == item) {
                isFirst = true;
                return false;
            }
        });

        return isFirst;
    } else if(state == 'close') {
        if ((currentItem + 1) == totalItems) {
            return true;
        }

        var isLast = false;

        array.forEach(function(item, index) {
            if (currentItem+1 == item) {
                isLast = true;
                return false;
            }
        });

        return isLast;
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
