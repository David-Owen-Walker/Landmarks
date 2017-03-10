<?php

require_once 'LandmarkTable.php';

/**
 * Landmark
 * @package: Omeka
 */
class Landmark extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
	public $title;
	public $description;
	public $credits;
	public $featured = 0;
	public $public = 0;
	public $slug;
	public $postscript_text;
	public $landmark_image;

	protected $_related = array( 'Items' => 'getItems','Image' => 'getImage' );

    /**
     * Identify Landmark records as relating to the Landmarks ACL resource.
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'Landmarks';
    }

	public function getItems()
	{
		return $this->getTable()->findItemsByLandmarkId( $this->id );
	}


	public function removeAllItems( ) {
		$db = get_db();
		$tiTable = $db->getTable( 'LandmarkItem' );
		$select = $tiTable->getSelect();
		$select->where( 'landmark_id = ?', array( $this->id ) );

		# Get the landmark item
		$landmarkItems = $tiTable->fetchObjects( $select );

		# Iterate through all the landmark items
		# and remove them
		for($i = 0; $i < count($landmarkItems); $i++) {
			$landmarkItems[$i]->delete();
		}
	}

	public function removeItem( $item_id )
	{
		if( !is_numeric( $item_id ) ) {
			$item_id = $item_id->id;
		}

		# First get the landmark-item object
		$db = get_db();
		$tiTable = $db->getTable( 'LandmarkItem' );
		$select = $tiTable->getSelect();
		$select->where( 'landmark_id = ?', array( $this->id ) )
		->where( 'item_id = ?', array( $item_id ) );

		# Get the landmark item
		$landmarkItem = $tiTable->fetchObject( $select );

		# Renumber any ordinals greater than it.
		$select = $tiTable->getSelect();
		$select->where( 'landmark_id = ?', array( $this->id ) )
		->where( 'ordinal > ?', array( $landmarkItem->ordinal ) );

		# Delete this linkage
		$landmarkItem->delete();

		# Reorder the remaining linkages
		$renumbers = $tiTable->fetchObjects( $select );
		foreach( $renumbers as $ti )
		{
			$ti->ordinal = $ti->ordinal - 1;
			$ti->save();
		}

	}

	public function addItem( $item_id, $ordinal = null )
	{
		if( !is_numeric( $item_id ) ) {
			$item_id = $item_id->id;
		}

		# Get the next ordinal
		$db = get_db();
		$tiTable = $db->getTable( 'LandmarkItem' );
		$select = $tiTable->getSelectForCount();
		$select->where( 'landmark_id = ?', array( $this->id ) );
		if($ordinal === null) {
			$ordinal = $tiTable->fetchOne( $select );
		}

		# Create, assign, and save the new landmark item connection
		$landmarkItem = new LandmarkItem;
		$landmarkItem->landmark_id = $this->id;
		$landmarkItem->item_id = $item_id;
		$landmarkItem->ordinal = $ordinal;
		$landmarkItem->save();
	}

	public function saveItemOrder( $landmark_id ) {

	}

	public function hoistItem( $landmark_id, $item_id )
	{
		$this->swapItem( $landmark_id, $item_id, true );
	}

	public function lowerItem( $landmark_id, $item_id )
	{
		$this->swapItem( $landmark_id, $item_id, false );
	}

	public function setItemOrdinal( $landmark_id, $item_id, $ordinal ) {
		$db = get_db();
		$tiTable = $db->getTable( 'LandmarkItem' );

		// Get the target item
		$select = $tiTable->getSelect()
		->where( 'landmark_id = ?', $landmark_id )
		->where( 'item_id = ?', $item_id );
		$item = $tiTable->fetchObject( $select );
		$item->ordinal = $ordinal;
		$item->save();
	}

	public function swapItem( $landmark_id, $item_id, $up )
	{
		$db = get_db();
		$tiTable = $db->getTable( 'LandmarkItem' );

		// Get the target item
		$select = $tiTable->getSelect()
		->where( 'landmark_id = ?', $landmark_id )
		->where( 'item_id = ?', $item_id );
		$left = $tiTable->fetchObject( $select );
		$ordinal = intval( $left->ordinal );

		// Get the next item with which we are swapping
		$select = $tiTable->getSelect()
		->where( 'landmark_id = ?', $landmark_id )
		->where( $up ? 'ordinal < ?' : 'ordinal > ?', $ordinal )
		->limit( 1 );
		$right = $tiTable->fetchObject( $select );

		// Do the ordinal shuffle
		$left->ordinal = intval( $right->ordinal );
		$right->ordinal = $ordinal;

		// Save both items
		$left->save();
		$right->save();
	}

	protected function _validate()
	{
		if( empty( $this->title ) ) {
			$this->addError( 'title', 'Landmark must be given a title.' );
		}

		if( strlen( $this->title > 255 ) ) {
			$this->addError( 'title', 'Title for a landmark must be 255 characters or fewer.' );
		}
		if (!$this->fieldIsUnique('title')) {
			$this->addError('title', 'The Title is already in use by another landmark. Please choose another.');
		}

		if( strlen( $this->slug > 30 ) ) {
			$this->addError( 'slug', 'Slug for a landmark must be 30 characters or fewer.' );
		}

		if( !empty($this->landmark_image) && !is_array(getimagesize( $this->landmark_image )) ){
			$this->addError('landmark_image','The text entered does not validate as an image URL.');
		}

		if( empty( $this->slug ) ) {
			if($title=$this->title){

			// replace non letter or digits by -
			$title = preg_replace('~[^\\pL\d]+~u', '-', $title);

			// trim
			$title = trim($title, '-');

			// transliterate
			$title = iconv('utf-8', 'us-ascii//TRANSLIT', $title);

			// lowercase
			$title = strtolower($title);

			// remove unwanted characters
			$title = preg_replace('~[^-\w]+~', '', $title);

				$this->slug= $title;
			}else{
				$this->addError( 'slug', 'Landmark must be given a slug.' );
			}
		}
		if (!$this->fieldIsUnique('slug')) {
			$this->addError('slug', 'The slug is already in use by another landmark. Please choose another.');
		}
	}
}
