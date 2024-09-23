/*global redux_change, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.osc_accordion = redux.field_objects.osc_accordion || {};

    $( document ).ready(
        function() {
            redux.field_objects.osc_accordion.init();
        }
    );

    $( window ).load(
        function() {
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



    redux.field_objects.osc_accordion.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( ".redux-group-tab" ).find( '.redux-oscitas-accordion' );
        }

        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;
                var id = el.attr('id');
                if (el.data('oscclose') == 1) {
                    //jQuery('#'+id+'-accordion-area').hide();
                }
                el.find('#'+id+'-header').click(function() {
                    jQuery('#'+id+'-accordion-area').slideToggle( "slow" );
                });
            }
        );
    };
})( jQuery );