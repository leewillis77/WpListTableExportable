<?php

namespace leewillis77\WpListTableExportable;

class TemplateLoader extends GamajoTemplateLoader {


	protected $filter_prefix             = 'wlte';
	protected $theme_template_directory  = 'wlte-templates';
	protected $plugin_directory          = '';
	protected $plugin_template_directory = 'templates';

	public function __construct() {
		$this->plugin_directory = __DIR__ . '/..';
	}

	/**
	 * Get the contents of a template with variables substituted.
	 *
	 * @param  string $slug      The template slug (First part of filename)
	 * @param  string $name      The template name (Second half of filename)
	 * @param  array  $variables (Optional) variables to be replaced into the template.
	 *
	 * @return string             The rendered output.
	 */
	public function get( $slug, $name = null, $variables = array() ) {
		ob_start();
		$this->get_template_part( $slug, $name );
		$content = ob_get_clean();
		foreach ( $variables as $key => $value ) {
			$content = str_replace( '{' . $key . '}', $value, $content );
		}
		return $content;
	}

	/**
	 * Output the contents of a template with variables substituted.
	 *
	 * @param  string $slug      The template slug (First part of filename)
	 * @param  string $name      The template name (Second half of filename)
	 * @param  array  $variables (Optional) variables to be replaced into the template.
	 *
	 * @uses   get_template_with_variables()
	 *
	 * @return string             The rendered output.
	 */
	public function output( $slug, $name = null, $variables ) {
		echo $this->get( $slug, $name, $variables );
	}
}
