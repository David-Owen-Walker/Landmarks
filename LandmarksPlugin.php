<?php

if( !defined( 'LANDMARKS_PLUGIN_DIR' ) )
{
	define( 'LANDMARKS_PLUGIN_DIR', dirname( __FILE__ ) );
}

class LandmarksPlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_filters = array('api_resources');

	protected $_hooks = array(
		'define_acl',
		'define_routes',
	);

	public function hookDefineAcl( $args )
	{
		$acl = $args['acl'];

		// Create the ACL context
		$acl->addResource( 'Landmarks' );

		// Allow anyone to look but not touch
		$acl->allow( null, 'Landmarks', array('browse', 'show') );

		// Allow contributor (and better) to do anything with landmarks
		$acl->allow( 'contributor','Landmarks');

	}

	public function hookDefineRoutes( $args )
	{
		$router = $args['router'];
		$router->addConfig( new Zend_Config_Ini(
				LANDMARKS_PLUGIN_DIR .
				DIRECTORY_SEPARATOR .
				'routes.ini', 'routes' ) );
	}

	public function filterPublicNavigationMain( $navs )
	{
		$navs[] = array(
			'label' => __('Landmarks'),
			'uri' => url( 'landmarks' ),
			'visible' => true
		);
		return $navs;
	}

	/**
     * Register the landmarks API resource. The resource can be indexed using a "near"
     * parameter. near is a lat/lng json object which, if provided, will sort
     * landmarks according to how close they are to the point provided.
     *
     * @param array $apiResources to add to
     * @return array the passed in array, with landmarks added
     */
    public function filterApiResources($apiResources)
    {
        $apiResources['landmarks'] = array(
            'record_type' => 'Landmark',
            'actions' => array('get', 'index'),
            'index_params' => array('near')
            
        );
        return $apiResources;
    }
}

include 'helpers/LandmarkFunctions.php';
