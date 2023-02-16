<?php

namespace ForGravity\Fillable_PDFs;

use Exception;
use GFAPI;
use GFCommon;
use WP_Error;

/**
 * Fillable PDFs PDF Generator class.
 *
 * @since     3.0
 * @package   FillablePDFs
 * @author    ForGravity
 * @copyright Copyright (c) 2021, ForGravity
 */
class Generator {

	/**
	 * The Feed object holding the configuration.
	 *
	 * @since 3.0
	 *
	 * @var array
	 */
	public $feed;

	/**
	 * The Entry object used for population.
	 *
	 * @since 3.0
	 *
	 * @var array
	 */
	public $entry;

	/**
	 * The Form object of the submitted entry.
	 *
	 * @since 3.0
	 *
	 * @var array
	 */
	public $form;

	/**
	 * Stores the Nested Form entries last retrieved entry index.
	 *
	 * @since 3.0
	 *
	 * @var array
	 */
	public $nested_form_entries_index = [];

	/**
	 * The template object getting populated.
	 *
	 * @since 3.0
	 *
	 * @var array
	 */
	protected $template;

	/**
	 * Path to the physical file of the generated PDF.
	 *
	 * @since 3.0
	 *
	 * @var string
	 */
	private $file_path;

	/**
	 * Generator constructor.
	 *
	 * @param $feed
	 * @param $entry
	 * @param $form
	 */
	public function __construct( $feed, $entry, $form ) {

		$this->feed  = $feed;
		$this->entry = $entry;
		$this->form  = $form;

		$this->hydrate_nested_form_entries();

	}

	/**
	 * Generates the PDF.
	 *
	 * @since 3.0
	 *
	 * @return bool|WP_Error
	 */
	public function generate() {

		if ( ( $template = $this->get_template() ) && is_wp_error( $template ) ) {
			return $template;
		}

		$args = [
			'template_id'   => $template['template_id'],
			'field_values'  => $this->get_field_values(),
			'file_name'     => $this->get_file_name(),
			'password'      => $this->get_password(),
			'user_password' => $this->get_user_password(),
			'permissions'   => rgars( $this->feed, 'meta/filePermissions' ),
			'flatten'       => rgars( $this->feed, 'meta/flatten', false ),
		];

		/**
		 * Modify the PDF that will be created.
		 *
		 * @since Unknown
		 *
		 * @param array $pdf_meta PDF arguments.
		 * @param array $feed     The feed object.
		 * @param array $entry    The entry object.
		 * @param array $form     The form object.
		 */
		$args = gf_apply_filters( [
			'fg_fillablepdfs_pdf_args',
			$this->form['id'],
		], $args, $this->feed, $this->entry, $this->form );

		if ( ! rgar( $args, 'file_name' ) ) {
			return new WP_Error( 'missing_file_name', esc_html__( 'A file name must be provided.', 'forgravity_fillablepdfs' ) );
		}

		$args['file_name'] = trim( $args['file_name'] );
		if ( empty( $args['file_name'] ) ) {
			return new WP_Error( 'missing_file_name', esc_html__( 'A file name must be provided.', 'forgravity_fillablepdfs' ) );
		}

		try {

			fg_fillablepdfs()->log_debug( __METHOD__ . '(): PDF to be generated: ' . print_r( $args, true ) );

			$pdf_contents = fg_fillablepdfs()->api->generate( $args['template_id'], $args );

			if ( empty( $pdf_contents ) ) {
				return new WP_Error( 'contents_empty', esc_html__( 'PDF could not be generated; empty file returned.', 'forgravity_fillablepdfs' ) );
			}

			fg_fillablepdfs()->log_debug( __METHOD__ . '(): PDF successfully generated.' );

		} catch ( \Exception $e ) {

			return new WP_Error( $e->getCode(), $e->getMessage() );

		}

		$written = $this->write_pdf( $pdf_contents );
		if ( is_wp_error( $written ) ) {
			return $written;
		}

		$entry_meta              = $this->save_entry_meta( $args );
		$entry_meta['file_path'] = $this->get_physical_file_path();

		/**
		 * Fires after PDF has been generated.
		 *
		 * @since Unknown
		 *
		 * @param array $pdf_meta PDF arguments.
		 * @param array $entry    The entry object.
		 * @param array $form     The form object.
		 * @param array $feed     The feed object.
		 */
		gf_do_action( [
			'fg_fillablepdfs_after_generate',
			$this->form['id'],
			$this->feed['id'],
		], $entry_meta, $this->entry, $this->form, $this->feed );

		return true;

	}




	// # GENERATE METHODS ----------------------------------------------------------------------------------------------

	/**
	 * Returns the collection of field values.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	private function get_field_values() {

		$values = [];

		foreach ( $this->get_mappings() as $field_name => $meta ) {

			// If mapped field is 0, skip.
			if ( '0' === (string) rgar( $meta, 'field' ) ) {
				continue;
			}

			$field_value = $this->get_field_value( $meta );

			if ( empty( $field_value ) ) {
				continue;
			}

			$values[ $field_name ] = $field_value;

		}

		return $values;

	}

	/**
	 * Returns the file name for the generated PDF.
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	protected function get_file_name() {

		$file_name = rgars( $this->feed, 'meta/fileName' );
		$file_name = GFCommon::replace_variables( $file_name, $this->form, $this->entry, false, false, false, 'text' );

		if ( ! preg_match( '/\.pdf$/i', $file_name ) ) {
			$file_name .= '.pdf';
		}

		return $file_name;

	}

	/**
	 * Returns the file password.
	 *
	 * @since 3.0
	 *
	 * @return string|null
	 */
	protected function get_password() {

		$password = rgars( $this->feed, 'meta/password', null );

		if ( ! $password ) {
			return null;
		}

		return GFCommon::replace_variables( $password, $this->form, $this->entry, false, false, false, 'text' );

	}

	/**
	 * Returns the file user password.
	 *
	 * @since 3.0
	 *
	 * @return string|null
	 */
	protected function get_user_password() {

		$password = rgars( $this->feed, 'meta/userPassword', null );

		if ( ! $password ) {
			return null;
		}

		return GFCommon::replace_variables( $password, $this->form, $this->entry, false, false, false, 'text' );

	}

	/**
	 * Writes the generated PDF to the file system.
	 *
	 * @since 3.0
	 *
	 * @param string $pdf_contents Contents of PDF file.
	 *
	 * @return bool|WP_Error
	 */
	protected function write_pdf( $pdf_contents ) {

		if ( ! is_string( $pdf_contents ) ) {
			return new WP_Error( 'invalid_contents', esc_html__( 'Unable to save PDF file; string not provided.', 'forgravity_fillablepdfs' ) );
		}

		$file_path = $this->get_physical_file_path();

		$writer = fopen( $file_path, 'w' );

		if ( ! $writer ) {
			return new WP_Error( 'writer_not_opened', esc_html__( 'Unable to save PDF file; writer could not be opened.', 'forgravity_fillablepdfs' ) );
		}

		$written = fwrite( $writer, $pdf_contents );
		fclose( $writer );

		if ( ! $written ) {
			return new WP_Error( 'writer_could_not_write', esc_html__( 'Unable to save PDF file; could not write to file.', 'forgravity_fillablepdfs' ) );
		}

		return true;

	}





	// # META METHODS --------------------------------------------------------------------------------------------------

	/**
	 * Returns the PDF meta for the provided feed.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	protected function get_entry_meta() {

		$meta = gform_get_meta( $this->entry['id'], 'fillablepdfs' );

		if ( ! rgar( $meta, $this->feed['id'] ) ) {
			return [];
		}

		$pdf_id = rgar( $meta, $this->feed['id'] );

		return gform_get_meta( $this->entry['id'], 'fillablepdfs_' . $pdf_id );

	}

	/**
	 * Stores the generated PDF to the entry's meta.
	 *
	 * @since 3.0
	 *
	 * @param array $args Generated PDF arguments.
	 *
	 * @return array
	 */
	protected function save_entry_meta( $args ) {

		global $wpdb;

		$current_meta = $this->get_entry_meta();
		$file_path    = $this->get_physical_file_path();

		$meta = [
			'pdf_id'             => rgar( $current_meta, 'pdf_id', uniqid() ),
			'feed_id'            => $this->feed['id'],
			'user_id'            => rgar( $current_meta, 'user_id' ) ? $current_meta['user_id'] : rgar( $this->entry, 'created_by', null ),
			'token'              => rgar( $current_meta, 'token', md5( uniqid( time() ) ) ),
			'file_name'          => $args['file_name'],
			'physical_file_name' => basename( $file_path ),
			'date_created'       => $wpdb->get_var( 'SELECT utc_timestamp()' ),
			'public'             => (bool) rgars( $this->feed, 'meta/publicAccess', false ),
		];

		gform_update_meta( $this->entry['id'], 'fillablepdfs_' . $meta['pdf_id'], $meta );

		// If PDF was previously generated, do not need to store PDF ID to entry meta.
		if ( ! empty( $current_meta ) ) {
			return $meta;
		}

		$entry_pdf_ids = gform_get_meta( $this->entry['id'], 'fillablepdfs' );
		$entry_pdf_ids = is_array( $entry_pdf_ids ) ? $entry_pdf_ids : [];

		if ( ! in_array( $meta['pdf_id'], $entry_pdf_ids ) ) {
			$entry_pdf_ids[ $this->feed['id'] ] = $meta['pdf_id'];
		}

		gform_update_meta( $this->entry['id'], 'fillablepdfs', $entry_pdf_ids );

		return $meta;

	}





	// # HELPER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Returns a Generator Field class name for a provided field ID.
	 *
	 * @since 3.0
	 *
	 * @param string $field_id Field ID.
	 *
	 * @return string
	 */
	public function get_field_class_name( $field_id ) {

		$field      = GFAPI::get_field( $this->form, $field_id );
		$base_class = 'ForGravity\Fillable_PDFs\Generator\Field\Base';

		if ( ! $field ) {
			return $base_class;
		}

		$field_type  = ucwords( $field->get_input_type() );
		$field_class = sprintf( '%s\%s', 'ForGravity\Fillable_PDFs\Generator\Field', $field_type );

		if ( class_exists( $field_class ) ) {
			return $field_class;
		}

		// %s_Field fallback for List field as List is a reserved keyword.
		if ( class_exists( sprintf( '%s_Field', $field_class ) ) ) {
			return sprintf( '%s_Field', $field_class );
		}

		return $base_class;

	}

	/**
	 * Returns the PDF field value from a set of mapping properties.
	 *
	 * @since 3.0
	 *
	 * @param array $mapping Mapping properties.
	 *
	 * @return string
	 */
	public function get_field_value( $mapping ) {

		/**
		 * @var Generator\Field\Base $field
		 */
		$field_class = $this->get_field_class_name( rgar( $mapping, 'field' ) );
		$field       = new $field_class( $this, $mapping );

		$value     = $field->get_value();
		$modifiers = $field->get_modifiers();

		// Apply legacy filters.
		$value = gf_apply_filters( [ 'gform_addon_field_value', $this->form['id'], $field->get_field_id() ], $value, $this->form, $this->entry, $field->get_field_id(), fg_fillablepdfs()->get_slug() );
		$value = gf_apply_filters( [ 'gform_' . fg_fillablepdfs()->get_slug() . '_field_value', $this->form['id'], $field->get_field_id() ], $value, $this->form, $this->entry, $field->get_field_id() );

		if ( ! empty( $modifiers ) && ! empty( $value ) ) {
			$value = GFCommon::implode_non_blank( ',', $modifiers ) . '|' . $value;
		}

		return $value;

	}

	/**
	 * Returns the field map for the feed, sorted by the detected template fields.
	 *
	 * @since 3.0
	 *
	 * @return array
	 */
	private function get_mappings() {

		$field_map       = rgars( $this->feed, 'meta/fieldMap' );
		$template_fields = [];

		$template = $this->get_template();
		if ( is_wp_error( $template ) ) {
			return [];
		}

		// Extract the template fields for all pages.
		foreach ( $template['meta']['pages'] as $page ) {
			$page_fields     = wp_list_pluck( $page['fields'], 'name' );
			$template_fields = array_merge( $template_fields, $page_fields );
		}

		// Remove unmapped template fields.
		$template_fields = array_filter( $template_fields, function( $field_name ) use ( $field_map ) {
			return rgar( $field_map, $field_name ) ? true : false;
		} );

		// Sort field map by template field order.
		return array_replace( array_flip( $template_fields ), $field_map );

	}

	/**
	 * Returns the physical file path for the generated PDF.
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	private function get_physical_file_path() {

		if ( $this->file_path ) {
			return $this->file_path;
		}

		$entry_meta = $this->get_entry_meta();

		if ( empty( $entry_meta ) ) {
			$this->file_path = fg_fillablepdfs()->generate_physical_file_path( $this->form['id'], $this->get_file_name(), $this->entry, true );
		} else {
			$this->file_path = fg_fillablepdfs()->generate_physical_file_path( $this->form['id'], $entry_meta['physical_file_name'], $this->entry, false );
		}

		return $this->file_path;

	}

	/**
	 * Returns the template for the feed.
	 *
	 * @since 3.0
	 *
	 * @return array|WP_Error
	 */
	private function get_template() {

		if ( $this->template !== null ) {
			return $this->template;
		}

		$template_id = rgars( $this->feed, 'meta/templateID' );

		// If API could not be initialized, return.
		if ( ! fg_fillablepdfs()->initialize_api() ) {
			return new WP_Error( 'api_not_initialized', esc_html__( 'PDF could not be generated because API could not be initialized.', 'forgravity_fillablepdfs' ) );
		}

		try {
			$this->template = fg_fillablepdfs()->api->get_template( $template_id );
		} catch ( Exception $e ) {
			return new WP_Error( 'template_not_found', sprintf( esc_html__( 'PDF template could not be retrieved; %s', 'forgravity_fillablepdfs' ), $e->getMessage() ) );
		}

		return $this->template;

	}

	/**
	 * Populate entry object with Nested Form entries.
	 *
	 * @since 3.0
	 */
	private function hydrate_nested_form_entries() {

		if ( ! GFAPI::get_fields_by_type( $this->form, 'form' ) ) {
			return;
		}

		foreach ( $this->entry as $meta_key => $meta_value ) {

			$field = GFAPI::get_field( $this->form, $meta_key );

			if ( ! $field || $field->type !== 'form' || empty( $meta_value ) ) {
				continue;
			}

			$entry_ids = is_array( $meta_value ) ? $meta_value : explode( ',', $meta_value );
			$entries   = [];

			foreach ( $entry_ids as $entry_id ) {
				$entry = GFAPI::get_entry( $entry_id );
				if ( ! is_wp_error( $entry ) ) {
					$entries[] = $entry;
				}
			}

			$this->entry[ $meta_key ] = $entries;

		}

	}

}