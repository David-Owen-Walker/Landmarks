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
        $etSelect->join(array( "el"=>$db->Element),
                        $etAlias . ".element_id = el.id AND " . $etAlias . ".record_id  = " . $record->id,
//                        . "AND " . $etAlias . "record_type  = 'Item'"
                        array("element_name"=>"el.name","element_id"=>"el.id")
                        
				)->join(array("es"=>$db->ElementSet),
                        "el.element_set_id = es.id",
                        array("element_set_name"=>"es.name","element_set_id"=>"es.id")
				);
        
        $taSelect = $taTable->getSelect();
        $taAlias = $taTable->getTableAlias();
        $taSelect->join(array( "rt"=>$db->RecordsTag),
                        "rt.record_id = " . $record->id . " AND rt.tag_id = " . $taAlias . ".id"
                        
				);
        
        $elementTexts = $etTable->fetchObjects( $etSelect );
        
        $etGenerator = function($elementText){
            $result = array(
                'text' => $elementText->text,
                'element_set' => array(
                    'id'=>$elementText->element_set_id,
                    'url'=> $this->getResourceUrl("/element_sets/" . $elementText->element_set_id),
                    'name'=>$elementText->element_set_name,
                    'resource'=>"element_sets",
                ),
                'element' => array(
                    'id'=>$elementText->element_id,
                    'url'=> $this->getResourceUrl("/elements/" . $elementText->element_id),
                    'name'=>$elementText->element_name,
                    'resource'=>"elements",
                ),
                'url' => $this->getResourceUrl("/items/{$tourItem->item_id}"),
                'resource' => 'items'
            );
            return $result;
        };
        
        $tags = $taTable->fetchObjects( $taSelect );
        
        $taGenerator = function($tag){
            $result = array(
                'id' => $tag->id,
                'url' => $this->getResourceUrl("/tags/{$tag->id}"),
                'name' => $tag->name,
                'resource' => 'tags'
            );
            return $result;
        };
        
        
        $representation['tags'] = array_map($taGenerator, $tags);
        $representation['element_texts'] = array_map($etGenerator, $elementTexts);
        
        
        return $representation;
    }
}