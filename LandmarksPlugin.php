<?php

if( !defined( 'LANDMARKS_PLUGIN_DIR' ) )
{
	define( 'LANDMARKS_PLUGIN_DIR', dirname( __FILE__ ) );
}

class LandmarksPlugin extends Omeka_Plugin_AbstractPlugin
{
	protected $_filters = array(
	        'api_resources',
		'public_navigation_main',
		'admin_dashboard_stats',
		'admin_navigation_main' );

	protected $_hooks = array(
		'define_acl',
		'define_routes',
		'admin_head',
		'admin_dashboard',
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

	public function filterAdminDashboardStats( $stats )
	{
		if( is_allowed( 'Landmarks', 'browse' ) )
		{
			$stats[] = array( link_to( 'landmarks', array(),
					total_records( 'Landmarks' ) ),
				__('landmarks') );
		}
		return $stats;
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

	public function hookAdminDashboard()
	{
		// Get the database.
		$db = get_db();

		// Get the Landmark table.
		$table = $db->getTable('Landmark');

		// Build the select query.
		$select = $table->getSelect();

		// Fetch some items with our select.
		$results = $table->fetchObjects($select);

		$landmarkItems = null;
		$html  = null;

		for($i=0;$i<=5;$i++){
			if(array_key_exists($i,$results) && is_object($results[$i])){
				$landmarkItems .='<div class="recent-row"><p class="recent"><a href="/admin/landmarks/show/'.$results[$i]->id.'">'
					.$results[$i]->title.'</a></p><p class="dash-edit"><a href="/admin/landmarks/edit/'.$results[$i]->id.'">Edit</a></p></div>';
			}
		}

		$html .= '<section class="five columns alpha"><div class="panel">';
		$html .= '<h2>'.__('Recent Landmarks').'</h2>';
		$html .= ''.$landmarkItems.'';
		$html .= '<p><a class="add-new-item" href="'.html_escape(url('landmarks/landmarks/add/')).'">'.__('Add a new landmark').'</a></p>';
		$html .= '</div></section>';

		echo $html;

	}





	public function hookAdminHead()
	{
		queue_css_file('landmark');
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

	public function filterAdminNavigationMain( $nav )
	{
		$nav['Landmarks'] = array( 'label' => __('Landmarks'),
			'action' => 'browse',
			'controller' => 'landmarks' );
		return $nav;
	}
}

include 'helpers/LandmarkFunctions.php';
