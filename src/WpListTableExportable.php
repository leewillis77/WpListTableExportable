<?php

namespace leewillis77\WPListTableExportable;

use leewillis77\WpListTableExportable\TemplateLoader;

class WpListTableExportable extends \WP_List_Table {

	protected $templates;

	protected $url_path;

	const WLTE_VERSION = '0.1';

	/**
	 * Constructor.
	 *
	 * Make a template loader available.
	 *
	 * @param mixed $args  Array or string of arguments.
	 */
	public function __construct( $args = array() ) {
		// Create a template loader instance.
		$this->templates = new TemplateLoader();

		// Work out the URL to the class assets.
		$this->url_path = plugin_dir_url(__FILE__);
		$this->url_path = explode( '/', $this->url_path );
		// Drop the src/ off the end
		array_pop($this->url_path);
		array_pop($this->url_path);
		$this->url_path = implode( '/', $this->url_path );

		parent::__construct( $args );

		// FIXME : How do we handle localisation?
		// (given we're not sure when we'll be instantiated?)
	}

	/**
	 * Display tablenav.
	 *
	 * Adds an export button.
	 *
	 * @param  string $which Whether we're generating the "top" or "bottom" tablenav.
	 */
	protected function display_tablenav( $which ) {
		parent::display_tablenav( $which );
		$this->templates->echo(
			'html',
			'export-link',
			array(
				'export_link' => $this->get_export_link(),
			)
		);
	}

	public function display() {
		wp_enqueue_style( 'wlte-admin', $this->url_path . '/css/wlte-admin.css', array(), self::WLTE_VERSION );
		parent::display();
	}

	/**
	 * Generate a URL to export the current view of the table.
	 */
	protected function get_export_link() {
		return add_query_arg( 'wplte_export', 1 );
	}
}