<?php
/**
 * Farm functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Farm
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function farm_setup() {
	add_theme_support(
		'custom-header',
		apply_filters(
			'farm_custom_header_args',
			array(
				'default-image'      => get_stylesheet_directory_uri() . '/images/milk-pitchers.jpg',
				'default-text-color' => '#111',
				'uploads'            => true,
				'width'              => '2000',
				'flex-height'        => true,
				'flex-width'         => true,
				'video'              => true,
			)
		)
	);

	register_default_headers(
		array(
			'default-image' => array(
				'url'           => get_stylesheet_directory_uri() . '/images/milk-pitchers.jpg',
				'thumbnail_url' => get_stylesheet_directory_uri() . '/images/milk-pitchers.jpg',
				'description'   => __( 'Milk Pitchers', 'farm' ),
			),
			'goat' => array(
				'url'           => get_stylesheet_directory_uri() . '/images/goat.jpg',
				'thumbnail_url' => get_stylesheet_directory_uri() . '/images/goat.jpg',
				'description'   => __( 'Goat', 'farm' ),
			),

			'cow' => array(
				'url'           => get_stylesheet_directory_uri() . '/images/cow.jpg',
				'thumbnail_url' => get_stylesheet_directory_uri() . '/images/cow.jpg',
				'description'   => __( 'Cow', 'farm' ),
			),

			'piglet' => array(
				'url'           => get_stylesheet_directory_uri() . '/images/piglet.jpg',
				'thumbnail_url' => get_stylesheet_directory_uri() . '/images/piglet.jpg',
				'description'   => __( 'Piglet', 'farm' ),
			),
		)
	);
}

add_action( 'after_setup_theme', 'farm_setup' );

/**
 * Register custom fonts.
 * Credits:
 * Twenty Seventeen WordPress Theme, Copyright 2016 WordPress.org
 * Twenty Seventeen is distributed under the terms of the GNU GPL
 */
function farm_fonts_url() {
	$fonts_url = '';

	/*
	 * Translators: If there are characters in your language that are not
	 * supported, translate this to 'off'. Do not translate
	 * into your own language.
	 */

	$dancing = _x( 'on', 'Dancing Script font: on or off', 'farm' );

	if ( 'off' !== $dancing ) {
		$font_families = array();
		$font_families[] = 'Dancing Script';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

/**
 * Enqueue scripts and styles.
 */
function farm_scripts() {
	wp_enqueue_style( 'farm-fonts', farm_fonts_url(), array(), null );
	wp_enqueue_style( 'farm-style', get_template_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'farm_scripts' );

/**
 * Add styles and fonts for the new editor.
 */
function farm_gutenberg_assets() {
	wp_enqueue_style( 'farm-gutenberg', get_theme_file_uri( 'gutenberg-editor.css' ), false );
	wp_enqueue_style( 'farm-fonts-gutenberg', farm_fonts_url(), array(), null );
}
add_action( 'enqueue_block_editor_assets', 'farm_gutenberg_assets' );

/**
 * Remove parent theme customizer options:
*/
function farm_customize_register( $wp_customize ) {

	$wp_customize->remove_control( 'embla_header_icon' );
	$wp_customize->remove_control( 'embla_icon_color' );
	$wp_customize->remove_control( 'embla_show_header_icon' );
	$wp_customize->remove_control( 'embla_show_footer_icon' );
	$wp_customize->remove_section( 'embla_support' );
	$wp_customize->remove_section( 'embla_font_options' );

	$wp_customize->add_section(
		'farm_staff',
		array(
			'title'       => __( 'Staff Template Settings', 'farm' ),
			'description' => __( '<b>This setting is specific for the staff page template.</b><br><br>Select the staff members that you would like to feature at the top of the page.', 'farm' ),
			'priority'    => 95,
		)
	);

	// Create a list of users / staff members.
	$users = get_users();
	$output = array();
	foreach ( (array) $users as $user ) {
		$output[ $user->ID ] = $user->display_name;
	}

	for ( $i = 1; $i < 9; $i++ ) {
		$wp_customize->add_setting(
			'farm_staff_member' . $i,
			array(
				'sanitize_callback' => 'farm_sanitize_select',
			)
		);

		$wp_customize->add_control(
			'farm_staff_member' . $i,
			array(
				'type'    => 'select',
				'label'   => __( 'Staff member #','farm' ) . $i,
				'section' => 'farm_staff',
				'choices' => $output,
			)
		);
	}

	// Register custom section types.
	$wp_customize->register_section_type( 'Embla_Customize_Section_Pro' );

	// Register sections.
	$wp_customize->add_section(
		new Embla_Customize_Section_Pro(
			$wp_customize,
			'embla_support',
			array(
				'pro_text'  => esc_html__( 'Rate this theme', 'farm' ),
				'pro_url'   => 'https://wordpress.org/support/theme/farm/reviews/#new-post',
				'pro_text2' => esc_html__( 'Visit the support forum', 'farm' ),
				'pro_url2'  => 'https://wordpress.org/support/theme/farm',
				'priority'  => '300',
			)
		)
	);

}
add_action( 'customize_register', 'farm_customize_register', 1000 );

/**
 * Sanitization callback for 'select' and 'radio' type controls. This callback sanitizes `$input`
 * as a slug, and then validates `$input` against the choices defined for the control.
 *
 * @see sanitize_key()               https://developer.wordpress.org/reference/functions/sanitize_key/
 * @see $wp_customize->get_control() https://developer.wordpress.org/reference/classes/wp_customize_manager/get_control/
 *
 * @param string               $input   Slug to sanitize.
 * @param WP_Customize_Setting $setting Setting instance.
 * @return string Sanitized slug if it is a valid choice; otherwise, the setting default.
 */
function farm_sanitize_select( $input, $setting ) {
	// Ensure input is a slug.
	$input = sanitize_key( $input );
	// Get list of choices from the control associated with the setting.
	$choices = $setting->manager->get_control( $setting->id )->choices;
	// If the input is a valid key, return it; otherwise, return the default.
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * /Note: This function is prefixed with the parent theme name to overwrite a parent theme function.
 */
function embla_footer() {
	?>
	<div class="credits">
		<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'farm' ) ); ?>" class="credit"><?php printf( esc_html__( 'Proudly powered by %s', 'farm' ), 'WordPress' ); ?></a>
		&nbsp; &nbsp;
		<a href="<?php echo esc_url( 'https://themesbycarolina.com' ); ?>" rel="nofollow" class="theme-credit"><?php printf( esc_html__( 'Theme: %1$s by Carolina', 'farm' ), 'Farm' ); ?></a>
	</div>
	<?php
}

/**
 * Custom CSS for the accent color.
 */
function farm_customize_css() {
	if ( get_theme_mod( 'embla_accent_color' ) !== '0073AA' ) {
		echo '<style type="text/css">';
		echo '.entry-title a:hover{	box-shadow: inset 0 0 0 0 ' . esc_attr( get_theme_mod( 'embla_accent_color' ) ) . ', 0 2px 0 0 ' . esc_attr( get_theme_mod( 'embla_accent_color' ) ) . '; }';
	}
}
add_action( 'wp_head', 'farm_customize_css' );
