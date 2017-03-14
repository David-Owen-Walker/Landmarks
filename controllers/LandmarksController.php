<?php
require_once 'Landmark.php';
require_once 'LandmarkItem.php';

class LandmarksController extends Omeka_Controller_AbstractActionController
{
	public function init()
	{
		$this->_helper->db->setDefaultModelName( 'Landmark' );
	}
    
    
    protected function _getRepresentation(
        Omeka_Record_Api_AbstractRecordAdapter $recordAdapter,
        Omeka_Record_AbstractRecord $record,
        $resource
    ) {
        $extend = array();
        $extendTemp = apply_filters("api_extend_items", array(), array('record' => $record));
        $apiResources = $this->getFrontController()->getParam('api_resources');
        // Validate each extended resource. Each must be registered as an API
        // resource and the content must contain "id" and "url" for one resource
        // or "count" and "url" for multiple resources. A "resource" is
        // recommended but not mandatory. Everything else passes through as
        // custom data that may be used for the client's convenience.
        foreach ($extendTemp as $extendResource => $extendContent) {
            if (is_array($extendContent)
                && array_key_exists($extendResource, $apiResources)
                && (array_key_exists('count', $extendContent) || array_key_exists('id', $extendContent))
                && array_key_exists('url', $extendContent)
            ) {
                $extend[$extendResource] = $extendContent;
            }
        }
        
        $representation['extended_resources'] = $extend;
    }

    public function removeitemAction()
	{
		// Get the landmark and item id from the request
		$landmark = $this->_helper->db->findById();
		$item_id = $this->getRequest()->getParam( 'item' );

		// Remove the item (id) from the landmark
		$landmark->removeItem( $item_id );

		// Go back to editing the landmark.
		$this->_redirectToEdit();
	}

	public function getitemsAction() {
		$db = get_db();
		$prefix=$db->prefix;
		$landmark = $this->_helper->db->findById();
		$itemTable = $db->getTable( 'Item' );
		$items = $itemTable->fetchObjects(
			"SELECT i.*, (SELECT count(*) FROM ".$prefix."landmark_items ti WHERE ti.item_id = i.id AND ti.landmark_id = ?) AS `in_landmark`
         FROM ".$prefix."items i",
			array( $landmark->id ) );

		foreach($items as $key => $arr) {
			$items[$key]['name'] = metadata( $arr, array( 'Dublin Core', 'Title' ) );
			$items[$key]['uri'] = record_url( $arr, 'show', true );
		}

		$itemsName = $this->view->pluralize( 'item' );
		$landmarkName = $this->view->singularize( $this->_helper->db->getDefaultModelName() );
		$this->view->assign( compact( 'items', 'landmark' ) );
	}

	public function browseforitemAction()
	{
		$db = get_db();
		$prefix=$db->prefix;
		$landmark = $this->_helper->db->findById();

		# Get all items which are not already in this landmark.
		$itemTable = $db->getTable( 'Item' );
		/* This did not work, much as I preferred
      $iAlias = $itemTable->getTableAlias();
      $select = $itemTable->getSelect();
      $select->joinLeft( array( 'ti' => $db->LandmarkItem ),
         "ti.item_id = $iAlias.id AND ti.landmark_id = ?" );
      $select->where( 'ti.id IS NULL' );
       */

		# Attach the items to the view
		#$items = $itemTable->fetchObjects( $select, array( $landmark_id ) );
		$items = $itemTable->fetchObjects( "SELECT i.*
         FROM ".$prefix."items i LEFT OUTER JOIN ".$prefix."landmark_items ti
         ON i.id = ti.item_id AND ti.landmark_id = ?
         WHERE ti.id IS NULL",
			array( $landmark->id ) );

		$itemsName = $this->view->pluralize( 'item' );
		$landmarkName = $this->view->singularize( $this->_helper->db->getDefaultModelName() );
		$this->view->assign( compact( 'items', 'landmark' ) );
	}

	public function additemAction()
	{
		# Get the landmark and item ids
		$landmark = $this->_helper->db->findById();
		$item_id = $this->getRequest()->getParam( 'item' );

		$landmark->addItem( $item_id );

		$this->_redirectToEdit();
	}

	public function hoistitemAction()
	{
		$landmark = $this->_helper->db->findById();
		$item_id = $this->getRequest()->getParam( 'item' );

		$landmark->hoistItem( $landmark->id, intval( $item_id ) );
		$this->_redirectToEdit();
	}

	public function loweritemAction()
	{
		$landmark = $this->_helper->db->findById();
		$item_id = $this->getRequest()->getParam( 'item' );

		$landmark->lowerItem( $landmark->id, intval( $item_id ) );
		$this->_redirectToEdit();
	}

	# Called only by AJAX at this point in time
	# so I don't do any setting of anything for the
	# view.
	public function savelandmarkitemsAction() {
		$landmark = $this->_helper->db->findById();

		# Remove all of the items in the landmark
		$landmark->removeAllItems();

		# Get our POST of the saveOrder
		$post = $this->getRequest()->getPost();
		$aOrder = json_decode($post['saveOrder'],true);

		# Iterate through all of the landmark items
		# passed in an add them to the landmark
		for($i = 0; $i < count($aOrder); $i++) {
			$item_id = intval( $aOrder[$i] );
			$landmark->addItem( $item_id, $i );
		}
	}

	private function _redirectToEdit()
	{
		$landmark_id = $this->getRequest()->getParam( 'id' );
		$this->_helper->redirector->gotoRoute(
			array( 'action' => 'edit',
				'id' => $landmark_id ),
			'landmarkAction' );
	}
}
