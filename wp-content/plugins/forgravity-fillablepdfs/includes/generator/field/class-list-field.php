<?php

namespace ForGravity\Fillable_PDFs\Generator\Field;

use ForGravity\Fillable_PDFs\Generator;

/**
 * Fillable PDFs PDF Generator List field value class.
 *
 * @since     3.0
 * @package   FillablePDFs
 * @author    ForGravity
 * @copyright Copyright (c) 2021, ForGravity
 */
class List_Field extends Base {

	/**
	 * Gravity Forms Field object.
	 *
	 * @since 3.0
	 *
	 * @var \GF_Field_List
	 */
	protected $field;

	/**
	 * Target List column.
	 *
	 * @since 3.0
	 *
	 * @var string
	 */
	private $column;

	/**
	 * Target List row.
	 *
	 * @since 3.0
	 *
	 * @var int
	 */
	private $row = -1;

	/**
	 * Parse the List field modifier.
	 *
	 * @since 3.0
	 *
	 * @param Generator $generator Instance of the PDF Generator.
	 * @param array     $mapping   Mapping properties.
	 */
	public function __construct( $generator, $mapping ) {

		parent::__construct( $generator, $mapping );

		foreach ( $this->modifiers as $i => $modifier ) {

			if ( substr( $modifier, 0, 5 ) !== 'list=' ) {
				continue;
			}

			unset( $this->modifiers[ $i ] );

			$exploded     = explode( ',', substr( $modifier, 5 ) );
			$this->column = $exploded[0];
			$this->row    = (int) $exploded[1] - 1;

		}

	}

	/**
	 * Returns the List row value.
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_value() {

		// If no row or column is defined, return default value.
		if ( ! $this->column && $this->row === -1 ) {
			return parent::get_value();
		}

		$field_value = rgar( $this->generator->entry, $this->field_id );
		$field_value = maybe_unserialize( $field_value );

		if ( $this->field->enableColumns ) {
			return rgars( $field_value, sprintf( '%d/%s', $this->row, $this->column ) );
		}

		return rgar( $field_value, $this->row );

	}

}
