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

	/**
	 * Display the list table.
	 */
	public function display() {

		// Add our stylesheet to style the export button.
		wp_enqueue_style( 'wlte-admin', $this->url_path . '/css/wlte-admin.css', array(), self::WLTE_VERSION );

		// If it's an online display, render as normal.
		if ( empty( $_GET['wlte_export'] ) ) {
			parent::display();
		} else {
			// Trash any output sent up until this point.
			ob_end_clean();

			// Output the download headers.
			$this->csv_headers();

			// Output the header row of the CSV.
			$this->print_column_headers_csv();

			// Output the data.
			$this->display_rows_csv();

			// Terminate processing.
			die();
		}
	}

	/**
	 * Output the headers to trigger a download.
	 */
	private function csv_headers() {
		if ( method_exists( $this, 'csv_filename' ) ) {
			$filename = call_user_func( array( $this, 'csv_filename' ) );
		} else {
			$filename = 'download-' . date('Y-m-d H:i:s') . '.csv';
		}
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
	}

	/**
	 * Output the CSV header row.
	 */
	private function print_column_headers_csv() {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
		$headers = array();
		foreach ( $columns as $column_key => $column_display_name ) {
			if ( in_array( $column_key, $hidden )  || 'cb' === $column_key ) {
				continue;
			}
			$headers[] = $column_display_name;
		}
		$this->put_csv( $headers );
	}

	/**
	 * Output the data as CSV rows.
	 */
	private function display_rows_csv() {
		if ( ! $this->has_items() ) {
			return;
		}
		foreach ( $this->items as $item ) {
			$this->single_row_csv( $item );
		}
	}

	/**
	 * Output a single row as CSV.
	 */
	private function single_row_csv( $item ) {
		list( $columns, $hidden, $sortable, $primary ) = $this->get_column_info();
		$row = array();
		foreach ( array_keys( $columns ) as $column_name ) {
			if ( in_array( $column_name, $hidden )  || 'cb' === $column_name ) {
				continue;
			}
			if ( method_exists( $this, 'column_csv_' . $column_name ) ) {
				$row[] = call_user_func( array( $this, 'column_csv_' . $column_name ), $item );
			} elseif ( method_exists( $this, '_column_' . $column_name ) ) {
				$row[] = $this->clean(
					call_user_func(
						array( $this, '_column_' . $column_name ),
						$item,
						'',
						'',
						$primary
					)
				);
			} elseif ( method_exists( $this, 'column_' . $column_name ) ) {
				$row[] = $this->clean(
					call_user_func( array( $this, 'column_' . $column_name ), $item )
				);
			} else {
				$row[] = $this->clean(
					$this->column_default( $item, $column_name )
				);
			}
		}
		$this->put_csv( $row );
	}

	/**
	 * Clean up a string for use in CSV.
	 *
	 * NOTE: This isn't about escaping, but it tidies up a string that was originally targetted at
	 * HTML output, and tries to make it CSV friendly.
	 */
	private function clean( $string ) {
		// Replace <br> with a space.
		$string = preg_replace( '#<br\s*/?>#i', ' ', $string );
		// Strip all other tags.
		$string = strip_tags( $string );
		// Decode any HTML entitites.
		$string = html_entity_decode( $string, ENT_QUOTES, 'UTF-8' );
		return $string;
	}

	/**
	 * Output an array using fputcsv to standard output.
	 */
	private function put_csv( $data ) {
		$out = fopen('php://output', 'w');
		fputcsv( $out, $data );
		fclose( $out );
	}

	/**
	 * Generate a URL to export the current view of the table.
	 */
	protected function get_export_link() {
		return add_query_arg( 'wlte_export', 1 );
	}
}