<?php

namespace ForGravity\Fillable_PDFs\Generator\Field;

use ForGravity\Fillable_PDFs\Generator;

/**
 * Fillable PDFs PDF Generator Signature field value class.
 *
 * @since     3.0
 * @package   FillablePDFs
 * @author    ForGravity
 * @copyright Copyright (c) 2021, ForGravity
 */
class Signature extends Base {

	/**
	 * Gravity Forms Field object.
	 *
	 * @since 3.0
	 *
	 * @var \GF_Field_Signature
	 */
	protected $field;

	/**
	 * Force image_fill modifier.
	 *
	 * @since 3.0
	 *
	 * @param Generator $generator Instance of the PDF Generator.
	 * @param array     $mapping   Mapping properties.
	 */
	public function __construct( $generator, $mapping ) {

		parent::__construct( $generator, $mapping );

		if ( ! in_array( 'image_fill', $this->modifiers ) ) {
			$this->modifiers[] = 'image_fill';
		}

	}

	/**
	 * Returns the Signature image URL, with the transparency query argument.
	 *
	 * @since 3.0
	 *
	 * @return string
	 */
	public function get_value() {

		$image_url = parent::get_value();

		return add_query_arg( [ 't' => 1 ], $image_url );

	}

}
