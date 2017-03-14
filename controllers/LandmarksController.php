<?php
class LandmarksController extends ApiController
{
	protected function _getRepresentation(
        Omeka_Record_Api_AbstractRecordAdapter $recordAdapter,
        Omeka_Record_AbstractRecord $record,
        $resource
    ) {
        $extend = array();
        $extendTemp = apply_filters("api_extend_$resource", array(), array('record' => $record));
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
        // Get the representation from the record adapter.
        $representation = $recordAdapter->getRepresentation($record);
//        $representation['extended_resources'] = $extend;
        return $representation;
    }
}
