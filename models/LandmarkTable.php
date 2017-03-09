<?php

class LandmarkTable extends Omeka_Db_Table
{

	public function getSelect()
	{
		# $this->getTable( 'Item' ) -> getSelect();
        $select = parent::getSelect()->order('tours.id');
		return $select;
	}
	public function getSelectForFindBy($params = array())
    	{
        	// $params = apply_filters($this->_getHookName('browse_params'), $params);
        	$select = $this->getSelect();
        	// $sortParams = $this->_getSortParams($params);
//		if ($params["near"]) {
//			$point = json_decode($params["near"]);
//
//			$latitude = $point->lat;
//			$longitude = $point->lng;
//			$dlat = "(l.latitude - " . $latitude . ")";
//			$dlng = "(l.longitude - " . $longitude . ")";
//			$distance = "(" . $dlat . "*" . $dlat . " + " . $dlng . "*" . $dlng . ")";
//
//			$db = get_db();
//			$alias = $this -> getTable( 'Item' ) -> getTableAlias();
//			//check if this is the right way to get the geo table
//
//			$select->join(array("l"=>$db->Location), "l.item_id = " . $alias . ".id", array("distance"=> $distance, "*"=>"*"));
//
//			$select->reset( Zend_Db_Select::ORDER );
//
//			$select->order("distance");
//		}
//		elseif ($sortParams) {
//            		list($sortField, $sortDir) = $sortParams;
//            		$this->applySorting($select, $sortField, $sortDir);
//            		if ($select->getPart(Zend_Db_Select::ORDER)
//                		&& $sortField != 'id'
//            		) {
//                		$alias = $this->getTableAlias();
//                		$select->order("$alias.id $sortDir");
//            		}
//        	}
//        	$this->applySearchFilters($select, $params);
        	//fire_plugin_hook($this->_getHookName('browse_sql'),
        	//                 array('select' => $select, 'params' => $params));
        	return $select;
    	}
}
