( function( $ ) {
    /**
     * Returns the Select2 version number which is used by ACF.
     *
     * @return int
     */
    function get_acf_select2_version() {
        if ( acf.isset( window, 'jQuery', 'fn', 'select2', 'amd' ) ) {
            return 4;
        }

        if ( acf.isset( window, 'Select2' ) ) {
            return 3;
        }

        return false;
    }

    function initialize_field( $el ) {
        var $el_select = $el.find( 'select' );
        var el_select_args = $el_select.data();
        var render_icon = function( id, text ) {
            var output = '';
            output += '<span class="select2-swp-acf-si">';
            output += '<svg \
                         class="select2-swp-acf-si__icon" \
                         aria-hidden="true" \
                         role="img" \
                    > \
                        <use xlink:href="' + el_select_args.file_url + '#' + id + '"></use> \
                    </svg>';
            output += text != '' ? '<span class="select2-swp-acf-si__name">' + text + '</span>' : '';
            output += '</span>';

            return output;
        };

        acf.add_filter( 'select2_args', function( select2_args, $select, args, $f ) {
            if ( $el_select === $select ) {
                /**
                 * Checks if it's the Select2 v4 or v3 which is used.
                 *
                 * https://stackoverflow.com/questions/26950588/select2-ajax-define-formatresult-formatselection-and-initselection-roles-and-b#answer-37890878
                 * https://select2.org/configuration/options-api
                 *
                 * It seems that since ACF Pro 5.7.0, `acf.select.version` doesn't exist anymore :/
                 * Now, ACF Pro uses `acf.newSelect2` which doesn't offer the Select2 version in its properties.
                 */
                var select2_version = acf.select2.version || get_acf_select2_version();

                if ( select2_version == 4 ) {
                    select2_args.templateResult = function( state ) {
                        // run default templateResult
                        var text = $.fn.select2.defaults.defaults.templateResult( state );

                        return render_icon( state.id, text );
                    };
                    select2_args.templateSelection = function( state ) {
                        return state.id ? render_icon( state.id, state.text ) : state.text;
                    };
                // v3
                } else {
                    select2_args.formatResult = function( result, container, query, escapeMarkup ) {
                        // run default formatResult
                        var text = $.fn.select2.defaults.formatResult( result, container, query, escapeMarkup );

                        return render_icon( result.id, text );
                    };
                    select2_args.formatSelection = function( object, $div ) {
                        return object.id ? render_icon( object.id, object.text ) : object.text;
                    };
                }
            }

            return select2_args;
        } );

        acf.select2.init(
            $el_select,
            el_select_args,
            $el
        );
    }

    if ( typeof acf.add_action !== 'undefined' ) {
        /**
         * ready append (ACF5)
         *
         * These are 2 events which are fired during the page load
         * ready = on page load similar to $(document).ready()
         * append = on new DOM elements appended via repeater field
         *
         * @type    event
         * @date    20/07/13
         *
         * @param   $el (jQuery selection) the jQuery element which contains the ACF fields
         * @return  n/a
         */
        acf.add_action( 'ready append', function( $el ) {
            // search $el for fields of type 'svg_icon'
            acf.get_fields( { type : 'svg_icon' }, $el ).each( function() {
                initialize_field( $( this ) );
            } );
        } );
    }
} )( jQuery );
