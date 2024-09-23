(function ($) {
    "use strict";

    if (typeof travelpayouts_suggest == "undefined") {
        return;
    }

    var source = "//autocomplete.travelpayouts.com/places2?locale="
        + travelpayouts_suggest.locale +
        "&types[]=city,airport";

    var cache = {};
    $(".travelpayouts-suggest").autocomplete({
        minLength: 2,
        source: function (request, response) {
            var term = encodeURIComponent(request.term);
            if (term in cache) {
                response(cache[term]);
                return;
            }

            $.getJSON(
                source,
                request, function (data, status, xhr) {
                    var airports = [];
                    $.each(data, function (key, value) {
                        airports.push({
                            label: value.name + ', ' + value.country_name + ' [' + value.code + ']',
                            value: value
                        });
                    });

                    cache[term] = airports;

                    response(airports);

                }
            ).fail(function () {
                response();
            });
        },
        select: function( event, ui ) {
            $(this).val(ui.item.label);
            $(this).parent().find('input[type="hidden"].select-raw').val(JSON.stringify(ui.item.value));
            return false;
        }
    });

})(jQuery);