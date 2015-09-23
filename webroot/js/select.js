$(function() {

    $('#filters').on('show.bs.collapse', function() {
        $(this).parents('.panel').find('.panel-title .glyphicon').first().attr('class', 'glyphicon glyphicon-chevron-down');
    });

    $('#filters').on('hide.bs.collapse', function() {
        $(this).parents('.panel').find('.panel-title .glyphicon').first().attr('class', 'glyphicon glyphicon-chevron-up');
    });

    $('.panel-heading').on('click', function() {
        $(this).parents('.panel').find('.panel-collapse').collapse('toggle');
    });

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

    $('[data-toggle="selection"]').on('click', function() {
        if ($(this).attr('disabled') == 'disabled') {
            return;
        }

        $(this).html('<span class="glyphicon glyphicon-refresh spinning"></span>').attr('disabled', 'disabled');

        var self = this;
        setTimeout(function() {
            if ($(self).data('state') == 'add') {
                $(self).html('<span class="glyphicon glyphicon-remove"></span>').removeAttr('disabled').attr('class', 'btn btn-danger pull-right').data('state', 'delete');
            } else {
                $(self).html('<span class="glyphicon glyphicon-plus"></span>').removeAttr('disabled').attr('class', 'btn btn-success pull-right').data('state', 'add');
            }
        }, 1000);
    });

    $('[data-toggle="tooltip"]').tooltip();

    setTimeout(function() {
        $('#filters').collapse('show');
    }, 500);

});
