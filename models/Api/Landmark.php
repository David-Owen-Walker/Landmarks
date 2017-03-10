<?php
/**
 */
class Api_Landmark extends Omeka_Record_Api_AbstractRecordAdapter
{
    /**
     * Get the REST representation of a landmark.
     * 
     * @param Landmark $record
     * @return array
     */
    public function getRepresentation(Omeka_Record_AbstractRecord $record)
    {

        $db = get_db();

        $tiTable = $db->getTable( 'LandmarkItem' );
        $glTable = $db->getTable( 'Location' );

        $tiSelect = $tiTable->getSelect();
        $tiSelect->where( 'landmark_id = ?', array( $record->id ) );

        # Get the landmark items
        $landmarkItems = $tiTable->fetchObjects( $tiSelect );

        $glSelect = $glTable->getSelect();
        $glSelect->where( 'item_id = ?', array( $landmarkItems[0]->item_id ) );
        $geolocations = $glTable->fetchObjects( $glSelect );
        $startLocation = $geolocations[0];

        #map landmark items to items
        $generator = function($landmarkItem){
            $result = array(
                'id' => $landmarkItem->item_id,
                'url' => $this->getResourceUrl("/items/{$landmarkItem->item_id}"),
                'resource' => 'items'
            );
            return $result;
        };

        $items = array_map($generator,$landmarkItems);

        $representation = array(
            'id' => $record->id,
            'url' => $this->getResourceUrl("/landmarks/{$record->id}"),
            'title' => $record->title,
            'description' => $record->description,
            'credits' => $record->credits,
            'public' => $record->public,
            'slug' => $record->slug,
            'postscript_text' => $record->postscript_text,
            'landmark_image' => $record->landmark_image,
            'items' => $items,
            'start' => $startLocation
        );
        return $representation;
    }
}
