/*global redux_change, redux*/

(function ($) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.osc_accordion = redux.field_objects.osc_accordion || {};

    $(document).ready(
        function () {
            redux.field_objects.osc_accordion.init();
        }
    );

    $(window).load(
        function () {
            var selector = $(document).find(".redux-group-tab").find('.redux-oscitas-accordion');
            $(selector).each(
                function () {
                    var el = $(this);
                    var parent = el;
                    var id = el.attr('id');
                    if (el.data('oscclose') == 1) {
                        jQuery('#' + id + '-accordion-area').hide();
                    }
                }
            );
        }
    );


    redux.field_objects.osc_accordion.init = function (selector) {
        if (!selector) {
            selector = $(document).find(".redux-group-tab").find('.redux-oscitas-accordion');
        }

        var removeTh = function (element) {
            var tr = $(element).parents('tr:first');
            var th = tr.find('th:first');
            if (th.html() && th.html().length > 0) {
                $(element).prepend(th.html());
                $(element).find('.redux_field_th').css('padding', '0 0 10px 0');
            }
            $(element).parent().attr('colspan', '2');
            th.remove();
        };


        $(selector).each(
            function () {
                var el = $(this);
                var parent = el;
                var id = el.attr('id');
                if (el.data('oscclose') == 1) {
                    jQuery('#' + id + '-accordion-area').hide();
                }
                el.find('#' + id + '-header').click(function () {
                    var accordionContainer = jQuery('#' + id + '-accordion-area');
                    jQuery(accordionContainer).find('.redux_remove_th').each(function (index, element) {
                        removeTh(element);

                    });
                    accordionContainer.slideToggle("slow", function () {
                        var accordionContent = $('#' + id + '-accordion-area');
                        if (accordionContent) {
                            jQuery(accordionContent).find('.redux_remove_th').each(function (index, element) {
                                // removeTh(element);
                                if (!accordionContainer.hasClass('osc-accordion-init')) {
                                    el.data('oscclose', false);
                                    $.redux.initFields();
                                    accordionContainer.addClass('osc-accordion-init');
                                }
                            });
                        }
                    });
                });
            }
        );
    };
})(jQuery);
