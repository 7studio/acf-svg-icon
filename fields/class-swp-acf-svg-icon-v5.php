<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die( 'Something went wrong.' );
}



if ( ! class_exists( 'swp_acf_field_svg_icon' ) )  {
    class swp_acf_field_svg_icon extends acf_field {
        /**
         * __construct
         *
         * This function will setup the field type data
         *
         * @type   function
         * @date   5/03/2014
         * @since  5.0.0
         *
         * @param  void
         * @return void
         */
        function __construct( $settings ) {
            // vars
            $this->name  = 'svg_icon';
            $this->label    = __( 'SVG Icon', 'swp-acf-si' );
            $this->category = 'choice';
            $this->defaults = array(
                'multiple'      => 0,
                'allow_null'    => 0,
                'choices'       => array(),
                'default_value' => '',
                'ui'            => 1,
                'ajax'          => 0,
                'placeholder'   => '',
                'return_format' => 'value'
            );
            $this->l10n = array(
                'matches_1'             => _x( 'One result is available, press enter to select it.', 'Select2 JS matches_1', 'acf'),
                'matches_n'             => _x( '%d results are available, use up and down arrow keys to navigate.',  'Select2 JS matches_n', 'acf'),
                'matches_0'             => _x( 'No matches found',   'Select2 JS matches_0', 'acf'),
                'input_too_short_1'     => _x( 'Please enter 1 or more characters', 'Select2 JS input_too_short_1', 'acf' ),
                'input_too_short_n'     => _x( 'Please enter %d or more characters', 'Select2 JS input_too_short_n', 'acf' ),
                'input_too_long_1'      => _x( 'Please delete 1 character', 'Select2 JS input_too_long_1', 'acf' ),
                'input_too_long_n'      => _x( 'Please delete %d characters', 'Select2 JS input_too_long_n', 'acf' ),
                'selection_too_long_1'  => _x( 'You can only select 1 item', 'Select2 JS selection_too_long_1', 'acf' ),
                'selection_too_long_n'  => _x( 'You can only select %d items', 'Select2 JS selection_too_long_n', 'acf' ),
                'load_more'             => _x( 'Loading more results&hellip;', 'Select2 JS load_more', 'acf' ),
                'searching'             => _x( 'Searching&hellip;', 'Select2 JS searching', 'acf' ),
                'load_fail'             => _x( 'Loading failed', 'Select2 JS load_fail', 'acf' ),
            );
            $this->settings = $settings;

            // do not delete!
            parent::__construct();
        }

        /**
         * render_field_settings()
         *
         * Create extra settings for your field. These are visible when editing a field
         *
         * @type   action
         * @since  3.6
         * @date   23/01/13
         *
         * @param  array $field The $field being edited
         * @return void
         */
        function render_field_settings( $field ) {
            // encode defaut_value (convert from array)
            $field['default_value'] = acf_encode_choices( $field['default_value'], false );

            // default_value
            acf_render_field_setting( $field, array(
                'label'         => __( 'Default Value', 'swp-acf-si' ),
                'instructions'  => __( 'Enter each default SVG Symbol ID on a new line', 'swp-acf-si' ),
                'name'          => 'default_value',
                'type'          => 'textarea',
                'placeholder'   => ''
            ) );

            // allow_null
            acf_render_field_setting( $field, array(
                'label'         => __( 'Allow Null?', 'swp-acf-si' ),
                'instructions'  => '',
                'name'          => 'allow_null',
                'type'          => 'true_false',
                'ui'            => 1,
            ) );

            // multiple
            acf_render_field_setting( $field, array(
                'label'         => __( 'Select multiple values?', 'swp-acf-si' ),
                'instructions'  => '',
                'name'          => 'multiple',
                'type'          => 'true_false',
                'ui'            => 1,
            ) );
        }

        /**
         * translate_field()
         *
         * This function will translate field settings
         *
         * @type   function
         * @date   8/03/2016
         * @since  5.3.2
         *
         * @param  array $field The field array holding all the field options
         * @return array $field
         */
        function translate_field( $field ) {
            // translate
            $field['choices'] = acf_translate( $field['choices'] );

            return $field;
        }

        /**
         * format_value()
         *
         * This filter is applied to the $value after it is loaded from the db and before it is returned to the template
         *
         * @type   filter
         * @since  3.6
         * @date   23/01/13
         *
         * @param  mixed $value   The value which was loaded from the database
         * @param  mixed $post_id The $post_id from which the value was loaded
         * @param  array $field   The field array holding all the field options
         * @return array $value   The modified value
         */
        function format_value( $value, $post_id, $field ) {
            if ( acf_is_array( $value ) ) {
                foreach ( $value as $i => $v ) {
                    $value[ $i ] = isset( $field['choices'][ $v ] ) ? $field['choices'][ $v ] : array();

                    if ( ! empty( $value[ $i ] ) ) {
                        $value[ $i ]['_file_url'] = $field['file']['url'];
                    }
                }
            } else {
                $value = isset( $field['choices'][ $value ] ) ? $field['choices'][ $value ] : array();

                if ( ! empty( $value ) ) {
                    $value['_file_url'] = $field['file']['url'];
                }
            }

            return $value;
        }

        /**
         * update_value()
         *
         * This filter is applied to the $value before it is updated in the db
         *
         * @type   filter
         * @since  3.6
         * @date   23/01/13
         *
         * @param  mixed $value   The value which will be saved in the database
         * @param  int   $post_id The $post_id of which the value will be saved
         * @param  array $field   The field array holding all the field options
         *
         * @return mixed $value (mixed) the modified value
         */
        function update_value( $value, $post_id, $field ) {
            if ( empty( $value ) ) {
                return $value;
            }

            if ( is_array( $value ) ) {
                // save value as strings, so we can clearly search for them in SQL LIKE statements
                $value = array_map( 'strval', $value );
            }

            return $value;
        }

        /**
         * load_field()
         *
         * This filter is applied to the $field after it is loaded from the database
         *
         * @type   filter
         * @date   23/01/2013
         * @since  3.6.0
         *
         * @param  array $field The field array holding all the field options
         * @return array $field
         */
        function load_field( $field ) {
            $field['file'] = array();

            $field['file']['path'] = apply_filters( "acf/fields/svg_icon/file_path", '', $field );
            $field['file']['path'] = apply_filters( "acf/fields/svg_icon/file_path/name={$field['_name']}", $field['file']['path'], $field );
            $field['file']['path'] = apply_filters( "acf/fields/svg_icon/file_path/key={$field['key']}", $field['file']['path'], $field );

            $field['file']['url'] = str_replace( get_theme_file_path(), get_theme_file_uri(), $field['file']['path'] );

            $field['choices'] = $this->parse_svg_sprite( $field['file']['path'] );

            $field['choices'] = apply_filters( "acf/fields/svg_icon/symbols", $field['choices'], $field );
            $field['choices'] = apply_filters( "acf/fields/svg_icon/symbols/name={$field['_name']}", $field['choices'], $field );
            $field['choices'] = apply_filters( "acf/fields/svg_icon/symbols/key={$field['key']}", $field['choices'], $field );

            return $field;
        }

        /**
         * update_field()
         *
         * This filter is applied to the $field before it is saved to the database
         *
         * @type   filter
         * @date   23/01/2013
         * @since  3.6.0
         *
         * @param  array $field The field array holding all the field options
         * @return array $field
         */
        function update_field( $field ) {
            $field['default_value'] = acf_decode_choices( $field['default_value'], true );

            return $field;
        }

        /**
         * render_field()
         *
         * Create the HTML interface for your field
         *
         * @param  $field (array) the $field being rendered
         *
         * @type   action
         * @since  3.6
         * @date   23/01/13
         *
         * @param  array $field The $field being edited
         * @return void
         */
        function render_field( $field ) {
            // convert to array
            $value = acf_get_array( $field['value'], false );

            // placeholder
            if ( empty( $field['placeholder'] ) ) {
                $field['placeholder'] = _n( 'Select icon', 'Select icons', ($field['multiple'] ? 7 : 1), 'swp-acf-si' );
            }

            // add empty value (allows '' to be selected)
            if ( empty( $value ) ) {
                $value = array( '' );
            }

            if ( empty( $field['choices'] ) ) {
                $v = $value;
                $v = $field['multiple'] ? implode( '||', $v ) : acf_maybe_get( $v, 0, '' );

                acf_hidden_input( array(
                    'id'    => $field['id'] . '-input',
                    'name'  => $field['name'],
                    'value' => $v
                ) );

                echo '<div class="acf-error-message -empty-choices"><div>' . __( 'You must choose an SVG sprite via the hook <pre><code>acf/fields/svg_icon/file_path</code></pre> to use this field correctly.', 'swp-acf-si' ) . '</div></div>';

                return;
            }

            // allow null
            if ( $field['allow_null'] && ! $field['multiple'] ) {
                $prepend = array( '' => array( 'title' => '- ' . $field['placeholder'] . ' -' ) );
                $field['choices'] = $prepend + $field['choices'];
            }

            $atts = array(
                'id'                => $field['id'],
                'class'             => $field['class'],
                'name'              => $field['name'],
                'data-file_url'     => $field['file']['url'],
                'data-ui'           => $field['ui'],
                'data-ajax'         => $field['ajax'],
                'data-multiple'     => $field['multiple'],
                'data-placeholder'  => $field['placeholder'],
                'data-allow_null'   => $field['allow_null']
            );

            // multiple
            if ( $field['multiple'] ) {
                $atts['multiple'] = 'multiple';
                $atts['size'] = 5;
                $atts['name'] .= '[]';
            }

            // special atts
            foreach ( array( 'readonly', 'disabled' ) as $att ) {
                if ( ! empty( $field[ $att ] ) ) {
                    $atts[ $att ] = $att;
                }
            }

            if ( ! empty( $field['ajax_action'] ) ) {
                $atts['data-ajax_action'] = $field['ajax_action'];
            }

            // hidden input
            $v = $value;
            $v = $field['multiple'] ? implode( '||', $v ) : acf_maybe_get( $v, 0, '' );

            acf_hidden_input( array(
                'id'    => $field['id'] . '-input',
                'name'  => $field['name'],
                'value' => $v
            ) );

            echo '<select ' . acf_esc_attr( $atts ) . '>';
                if ( ! empty( $field['choices'] ) ) {
                    foreach( $field['choices'] as $k => $v ) {
                        $pos = array_search( esc_attr( $k ), $value );
                        $label = isset( $v['title'] ) ? $v['title'] : '';
                        $atts = array( 'value' => $k );

                        if ( ! empty( $v ) ) {
                            foreach ( $v as $dk => $dv ) {
                                if ( $dk == 'title' ) {
                                    continue;
                                }

                                $atts[ 'data-' . $dk ] = $dv;
                            }
                        }

                        if ( $pos !== false ) {
                            $atts['selected'] = 'selected';
                            $atts['data-i'] = $pos;
                        }

                        echo '<option ' . acf_esc_attr( $atts ) . '>' . $label . '</option>';
                    }
                }
            echo '</select>';
        }

        /**
         * input_admin_enqueue_scripts()
         *
         * This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
         * Use this action to add CSS + JavaScript to assist your render_field() action.
         *
         * @type   action (admin_enqueue_scripts)
         * @since  3.6
         * @date   23/01/13
         *
         * @param  void
         * @return void
         */
        function input_admin_enqueue_scripts() {
            global $wp_scripts;

            // bail ealry if the library can't be no enqueue
            if ( ! acf_get_setting( 'enqueue_select2' ) ) {
                return;
            }

            $url = $this->settings['url'];
            $version = $this->settings['version'];
            $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            $select2_major_version = acf_get_setting( 'select2_version' );
            $select2_version = '';
            $select2_script = '';
            $select2_style = '';

            // attempt to find 3rd party Select2 version
            // - avoid including v3 CSS when v4 JS is already enququed
            if ( isset( $wp_scripts->registered['select2'] ) ) {
                $select2_major_version = (int) $wp_scripts->registered['select2']->ver;
            }

            // v4
            if ( $select2_major_version == 4 ) {
                $select2_version = '4.0';
                $select2_script = acf_get_dir( "assets/inc/select2/4/select2.full{$min}.js" );
                $select2_style = acf_get_dir( "assets/inc/select2/4/select2{$min}.css" );
            // v3
            } else {
                $select2_version = '3.5.2';
                $select2_script = acf_get_dir( "assets/inc/select2/3/select2{$min}.js" );
                $select2_style = acf_get_dir( "assets/inc/select2/3/select2.css" );
            }

            wp_enqueue_script( 'select2', $select2_script, array( 'jquery' ), $select2_version );
            wp_enqueue_style( 'select2', $select2_style, '', $select2_version );

            wp_enqueue_style( 'swp-acf-si', "{$url}assets/css/input{$min}.css", array( 'select2', 'acf-input' ), $version );
            wp_enqueue_script( 'swp-acf-si', "{$url}assets/js/input{$min}.js", array( 'select2', 'acf-input' ), $version, true );
        }

        /**
         * parse_svg_sprite()
         *
         * Extract icons from svg file.
         *
         * @type   function
         * @since  1.0.0
         * @date   30/05/18
         *
         * @param  string $file_path Full path to the SVG sprite
         * @return array  $icons
         */
        public function parse_svg_sprite( $file_path = '' ) {
            if ( ! file_exists( $file_path ) ) {
                return array();
            }

            $icons = array();

            // Try to get icons from the cache
            $key_suffix    = md5( $file_path );
            $cachekey      = 'swp_acf_svg_icon_' . $key_suffix;
            $cachekey_time = 'swp_acf_svg_icon_time_' . $key_suffix;

            if ( false !== ( $_time = get_transient( $cachekey_time ) )
                && filemtime( $file_path ) <= $_time
                && false !== ( $icons = get_transient( $cachekey )  ) )
            {
                return $icons;
            }

            // Get SVG sprite content
            $content = file_get_contents( $file_path );
            // Remove all HTML comments except the plugin' ones (`<!-- swp-acf-si: -->`)
            $content = preg_replace( '/<!--\s*((?!swp-acf-si:).|\n)*\s*-->/', '', $content );
            // Strip all unneeded HTML tags (thanks: )
            $content = wp_kses( $content, array( 'symbol' => array( 'id' => array() ) ) );

            // Get all symbols and prepare them
            $symbols = array();
            preg_match_all( '/id="(\S+)"/', $content, $matches );
            foreach ( $matches[1] as $match ) {
                $symbols[ $match ] = array( 'ID' => $match );
            }

            // Get all symbols' data
            $data = array();
            preg_match_all( '/<!--\s*swp-acf-si:(.*?)\s*(\{.*?\}){1}\s*-->/', $content, $_comments );
            foreach ( $_comments[0] as $index => $_comment ) {
                $key = $_comments[1][ $index ];

                $data[ $key ] = json_decode( $_comments[2][ $index ], true );

                if ( $data[ $key ] === null ) {
                    unset( $data[ $key ] );
                }
            }

            // Merge data into symbols
            $icons = array_merge_recursive( $symbols, $data );

            // Cache the result until the file is modified
            set_transient( $cachekey_time, time(), YEAR_IN_SECONDS );
            set_transient( $cachekey, $icons, YEAR_IN_SECONDS );

            return $icons;
        }
    }

    new swp_acf_field_svg_icon( $this->settings );
}
