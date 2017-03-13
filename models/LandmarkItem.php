<?php

/**
 * Landmark Item.
 * @package: Omeka
 */
class LandmarkItem extends Omeka_Record_AbstractRecord
{
	public $landmark_id;
	public $item_id;
	public $ordinal = 0;

	protected $_related = array(
		'Landmark' => 'getLandmark',
		'Item' => 'getItem',
	);

	protected function getItem()
	{
		return $this->getTable( 'Item' )->find( $this->item_id );
	}

	protected function getLandmark()
	{
		return $this->getTable( 'Landmark' )->find( $this->landmark_id );
	}

	protected function _validate()
	{
		if( empty( $this->item_id ) ) {
			$this->addError( 'item_id', 'Landmark item requires an item ID#' );
		}

		if( ! is_numeric( $this->item_id ) ) {
			$this->addError( 'item_id', 'Item ID must be numeric' );
		}

		if( empty( $this->landmark_id ) ) {
			$this->addError( 'landmark_id', 'Landmark item requires a landmark ID#' );
		}

		if( ! is_numeric( $this->landmark_id ) ) {
			$this->addError( 'landmark_id', 'Landmark ID must be numeric' );
		}

		if( ! is_numeric( $this->ordinal ) ) {
			$this->addError( 'ordinal', 'Order must be numeric' );
		}
	}
}
