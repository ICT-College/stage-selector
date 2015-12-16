var autocompletes = [];

function generateUUID() {
    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x3|0x8)).toString(16);
    });
    return uuid;
};

$(function () {
    $('[data-autocomplete-url]').each(function () {
        $(this).data('autocomplete-uuid', generateUUID());

        var autocomplete = {};
        autocomplete.keyField = $(this).data('autocomplete-key') ? $(this).data('autocomplete-key') : 'id';
        autocomplete.valueField = $(this).data('autocomplete-value') ? $(this).data('autocomplete-value') : 'name';
        autocomplete.timeout = 0;
        autocomplete.awesomplete = new Awesomplete($(this)[0], {
            autoFirst: true,
            filter: function () {
                return true;
            }
        });

        $(this)[0].addEventListener('awesomplete-selectcomplete', function () {
            var valueString = $(this).val().split(' - ');

            if ($(this).data('autocomplete-key') != 'none') {
                var key = valueString[0].replace(/\D/g, '');
                var value = valueString.slice(1).join(' - ');

                $('[data-autocomplete-id=' + $(this).data('autocomplete-value-id') + ']').val(key);
            } else {
                var value = valueString;
            }

            $(this).val(value);
        });

        if ($(this).data('autocomplete-strict') != '0') {

            $(this).on('blur', function (e) {
                var key = '';
                var value = '';

                if ($(this).val() != '') {
                    var valueString = $(this).closest('.awesomplete').find('li[aria-selected="true"]').text().split(' - ');
                    key = valueString[0].replace(/\D/g, '');
                    value = valueString.slice(1).join(' - ');
                }

                $(this).val(value);
                $('[data-autocomplete-id=' + $(this).data('autocomplete-value-id') + ']').val(key);
            });

        }

        $(this).on('keyup', function(e) {
            if ([13, 40, 38].indexOf(e.keyCode) != -1) {
                return;
            }

            var uuid = $(this).data('autocomplete-uuid');
            if (autocompletes[uuid].timeout != null) {
                clearTimeout(autocompletes[uuid].timeout);
            }

            autocompletes[uuid].awesomplete.list = [];

            var self = $(this);
            autocompletes[uuid].timeout = setTimeout(function() {
                var query = self.val();

                $.get(self.data('autocomplete-url'), { limit: 5, q: query }, function(data) {
                    if (data.success) {
                        var results = data.data.map(function(e) {
                            if (autocompletes[uuid].keyField == 'none') {
                                return e[autocompletes[uuid].valueField];
                            } else {
                                return e[autocompletes[uuid].keyField] + ' - ' + e[autocompletes[uuid].valueField];
                            }
                        });

                        autocompletes[self.data('autocomplete-uuid')].awesomplete.list = results;
                    } else {
                        autocompletes[self.data('autocomplete-uuid')].awesomplete.list = [];
                    }
                });
            }, 50);
        });

        autocompletes[$(this).data('autocomplete-uuid')] = autocomplete
    });
});
