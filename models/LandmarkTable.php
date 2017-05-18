<?php

class LandmarkTable extends Omeka_Db_Table
{
    
    public function setTableName($name = null)
    {
        $this->_name = 'items';
    }
    
    public function getSelect() {
        $select = get_db()->getTable("Item")->getSelect();
        $select->where($this->getTableAlias.".public = true");
        return $select;
    }
    
	public function getSelectForFindBy($params = array())
    	{
        $select = $this->getSelect();
		if ($params["near"]) {
			// Build an expression that evaluates to the SQUARE of the distance from the given point
			// Distance is measured in units of latitude (~111km) as though the earth were flat
			$point = json_decode($params["near"]);
			$latitude = $point->lat;
			$longitude = $point->lng;
			$dlat = "(loc.latitude - " . $latitude . ")";
			$scale = cos(deg2rad($latitude));
			$dlng = "((loc.longitude - " . $longitude . ")*".$scale.")";
			$distance = "(" . $dlat . "*" . $dlat . " + " . $dlng . "*" . $dlng . ")";

			$db = get_db();
			$alias = $this->getTableAlias();

			$select->join(array("loc"=>$db->Location),
                          "loc.item_id = " . $alias .".id AND ".$alias.".public = true",
                          array("distance"=> $distance));

			$select->reset( Zend_Db_Select::ORDER );

			$select->order("distance");
		}
		elseif ($sortParams) {
            list($sortField, $sortDir) = $sortParams;
            $this->applySorting($select, $sortField, $sortDir);
            if ($select->getPart(Zend_Db_Select::ORDER)
                && $sortField != 'id'
            ) {
                $alias = $this->getTableAlias();
                $select->order("$alias.id $sortDir");
            }
        }
        $this->applySearchFilters($select, $params);
        return $select;
    }
}
