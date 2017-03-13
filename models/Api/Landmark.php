<?php
/**
 */
class Api_Landmark extends Omeka_Record_Api_AbstractRecordAdapter
{
    
    /**
     * Initialize the mixins.
     */
    protected function _initializeMixins()
    {
        $this->_mixins[] = new Mixin_Tag($this);
        $this->_mixins[] = new Mixin_Owner($this);
        $this->_mixins[] = new Mixin_ElementText($this);
        $this->_mixins[] = new Mixin_PublicFeatured($this);
        $this->_mixins[] = new Mixin_Timestamp($this);
        $this->_mixins[] = new Mixin_Search($this);
    }
    
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
        
        $db = get_db();
        $etTable = $db->getTable( 'ElementText' );
        $taTable = $db->getTable( 'Tag' );
        $etSelect = $etTable->getSelect();
        $etAlias = $etTable->getTableAlias();
        
        $etSelect->join(array( "el"=>$db->Element, "es"=>$db->ElementSet),
                        $etAlias . ".element_id = el.id AND el.element_set_id = es.id
                        AND " . $etAlias . "record_id  = " . $record->id
//                        . "AND " . $etAlias . "record_type  = 'Item'"
				);
                
        # Get the tour items
        $elementTexts = $etTable->fetchObjects( $etSelect );
        
        $generator = function($elementText){
            $result = array(
                'text' => $elementText->text,
                'url' => $this->getResourceUrl("/items/{$tourItem->item_id}"),
                'resource' => 'items'
            );
            return $result;
        };
        
        $representation['tags'] = $taTable->findBy(array('record' => $record));
        $representation['element_texts'] = $texts;
        $representation['debug'] = get_object_vars($record);
        return $representation;
    }
}
