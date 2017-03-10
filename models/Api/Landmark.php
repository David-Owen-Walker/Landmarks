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
        $representation = array(
            'id' => $record->id,
            'url' => self::getResourceUrl("/items/{$record->id}"),
            'public' => (bool) $record->public,
            'featured' => (bool) $record->featured,
            'added' => self::getDate($record->added),
            'modified' => self::getDate($record->modified),
        );
        if ($record->item_type_id) {
            $representation['item_type'] = array(
                'id' => $record->item_type_id,
                'url' => self::getResourceUrl("/item_types/{$record->item_type_id}"),
                'name' => $record->Type->name,
                'resource' => 'item_types',
            );
        } else {
            $representation['item_type'] = null;
        }
        if ($record->collection_id) {
            //check that user has access to the collection
            $collection = $record->getCollection();
            if (is_allowed($collection, 'show')) {
                $representation['collection'] = array(
                    'id' => $record->collection_id,
                    'url' => self::getResourceUrl("/collections/{$record->collection_id}"),
                    'resource' => 'collections',
                );
            } else {
                $representation['collection'] = null;
            }
        } else {
            $representation['collection'] = null;
        }
        if ($record->owner_id) {
            $representation['owner'] = array(
                'id' => $record->owner_id,
                'url' => self::getResourceUrl("/users/{$record->owner_id}"),
                'resource' => 'users',
            );
        } else {
            $representation['owner'] = null;
        }
        $representation['files'] = array(
            'count' => $record->getTable('File')
                ->count(array('item_id' => $record->id)),
            'url' => self::getResourceUrl("/files?item={$record->id}"),
            'resource' => 'files',
        );
//        $representation['tags'] = $this->getTagRepresentations($record);
//        $representation['element_texts'] = $this->getElementTextRepresentations($record);
        return $representation;
    }
}
