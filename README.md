# ACF SVG Icon Field

Welcome to the Advanced Custom Fields SVG Icon repository on Github.

First ideas of this plugin come from [BeAPI/acf-svg-icon](https://github.com/beapi/acf-svg-icon)
but they were reorganised and enhanced by my mind :yum:

## Description

Add a new ACF field type: "SVG Icon" which allows you to select icon(s) from a SVG sprite.

![ACF SVG Icon Field](http://www.7studio.fr/github/acf-svg-icon/screenshot-1.png)

**This plugin works only with the [ACF PRO](https://www.advancedcustomfields.com/pro/) (version 5.5.0 or higher).**

## Usage

### Choose a SVG file for a specific field

There are three possible ways to use this feature.

1. `acf/fields/svg_icon/file_path` - filter for every field
2. `acf/fields/svg_icon/file_path/name={$field_name}` - filter for a specific field based on its name
3. `acf/fields/svg_icon/file_path/key={$field_key}` - filter for a specific field based on its key

```php
add_filter( 'acf/fields/svg_icon/file_path', 'tc_acf_svg_icon_file_path' );
function tc_acf_svg_icon_file_path( $file_path ) {
    return get_theme_file_path( 'assets/icons/icons.svg' );
}
```

### Translate the SVG text alternatives

There are four possible ways to use this feature.

The first three ones are offered by filter hooks like all ACF fields:

1. `acf/fields/svg_icon/symbols` - filter for every field
2. `acf/fields/svg_icon/symbols/name={$field_name}` - filter for a specific field based on its name
3. `acf/fields/svg_icon/symbols/key={$field_key}` - filter for a specific field based on its key

```php
add_filter( 'acf/fields/svg_icon/symbols/name=icon', 'tc_acf_svg_icon_symbol' );
function tc_acf_svg_icon_symbol( $symbols ) {
    $symbols_data = array(
        'IconTwitter' => array(
        	'title' => 'Twitter'
        ),
        'IconFacebook' => array(
        	'title'         => 'Facebook',
        	'default_color' => '#3b5998'
        )
    );

    foreach ( $symbols_data as $id => $data ) {
        if ( array_key_exists( $id, $symbols ) ) {
            $symbols[ $id ] = array_merge( $symbols[ $id ], $data );
        }
    }

    return $symbols;
}
```

By the way, you can also use this filter to reduce the list of SVG symbols ;)

But if you are lazy (like me) and you are afraid of forgetting to update your filter hook for translations, you can use the fourth (and the better) way:

```html
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
    <defs>
        <path id="BackgroundShape" fill="#efefef" stroke="none" d="…" />
    </defs>
    <!--swp-acf-si:IconTwitter {"title":"Twitter"}-->
    <symbol id="IconTwitter" viewBox="0 0 256 256">
        <use xlink:href="#BackgroundShape" />
        <path fill="currentColor" stroke="none" d="…"/>
    </symbol>
    <symbol id="IconFacebook" viewBox="0 0 256 256">
        <!-- swp-acf-si:IconFacebook {"title":"Facebook", "default_color":"#3b5998"} -->
        <use xlink:href="#BackgroundShape" />
        <g fill="currentColor" stroke="none">
            <path d="…"/>
            <path d="…"/>
        </g>
    </symbol>
</svg>
```

You are able to define all your symbols' data into a special HTML comment `<!-- swp-acf-si:{symbol_ID} {key:value} -->` (like Gutenberg settings storage) for each symbols.

## Tips to display icon

```html
<?php $icon = get_field( 'icon' ); ?>
<div class="Icon" style="color:<?php echo esc_attr( $icon['default_color'] ); ?>">
    <svg widht="64" height="64">
        <title><?php echo esc_html( $icon['title'] ); ?></title>
        <use xlink:href="<?php echo esc_url( "{$icon['_file_url']}#{$icon['ID']}" ); ?>"></use>
    </svg>
</div>
```

## Caution

If you use SVGO or something else to optimise your SVG files, you should turn off the remove comments option to keep the plugin extra comments.

## SVGO usage/plugin

If you use SVGO and allowed to add a custom task, you can copy/paste the code below to turn off the `removeComments` task and register a new one which removes comments except if it's important (default behaviour `<!--! my important comment -->`) and if it's needed by ACF SVG Icon Field as well.

```js
{
  removeComments: false
},
{
  stripComments: {
    type: 'perItem',
    description: 'strips comments',
    params: {},
    fn: (item, params) => {
        if (!item.comment) {
          return;
        }

        if (item.comment.charAt(0) !== '!' && ! /^swp-acf-si:/.test(item.comment)) {
          return false;
        }
    }
  }
}
```

## Changelog

### 1.0.1 (September 7, 2018)
* Add compatibility for ACF Pro 5.7.x
* Introduce SVGO custom task `stripComments` 
* Fix mistakes in README

### 1.0.0 (May 31, 2018)
* Initial Release.
