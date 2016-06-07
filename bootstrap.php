<?php

/**
 * This file bootstraps things needed by the class.
 *
 * Typically these are things that need to happen early enough that we cannot
 * rely on doing it when the class is instantiated. Examples:
 *     * Registering translation domains
 *     * Output buffering so we can drop any generated HTML
 */

if ( ! function_exists( 'wlte_init' ) ) {
	function wlte_init() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wlte' );
		load_textdomain( 'wlte', WP_LANG_DIR.'/wp-list-table-exportable/wlte-' . $locale . '.mo' );
		load_plugin_textdomain( 'wlte', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	add_action( 'init', 'wlte_init' );
}

if ( ! defined( 'WLTE_BUFFERING_ACTIVE' ) && !empty( $_GET['wlte_export'] ) ) {
	ob_start();
	define( 'WLTE_BUFFERING_ACTIVE', true );
}
