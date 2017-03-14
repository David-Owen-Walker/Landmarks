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
		'install',
		'uninstall',
		'define_acl',
		'define_routes',
		'admin_head',
		'admin_dashboard',
		'upgrade',
	);

	public function hookInstall()
	{
		$db = $this->_db;

		$landmarkQuery = "
         CREATE TABLE IF NOT EXISTS `$db->Landmark` (
            `id` int( 10 ) unsigned NOT NULL auto_increment,
            `title` varchar( 255 ) collate utf8_unicode_ci default NULL,
            `description` text collate utf8_unicode_ci NOT NULL,
            `credits` text collate utf8_unicode_ci,
            `landmark_image` text collate utf8_unicode_ci,
            `postscript_text` text collate utf8_unicode_ci,
            `featured` tinyint( 1 ) default '0',
            `public` tinyint( 1 ) default '0',
            `slug` varchar( 30 ) collate utf8_unicode_ci default NULL,
            PRiMARY KEY( `id` ),
            UNIQUE KEY `slug` ( `slug` )
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ";

		$landmarkItemQuery = "
         CREATE TABLE IF NOT EXISTS `$db->LandmarkItem` (
            `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `landmark_id` INT( 10 ) UNSIGNED NOT NULL,
            `ordinal` INT NOT NULL,
            `item_id` INT( 10 ) UNSIGNED NOT NULL,
            PRIMARY KEY( `id` ),
            KEY `landmark` ( `landmark_id` )
         ) ENGINE=InnoDB ";

		$db->query( $landmarkQuery );
		$db->query( $landmarkItemQuery );
	}

	public function hookUninstall()
	{
		$db = $this->_db;
		$db->query( "DROP TABLE IF EXISTS `$db->LandmarkItem`" );
		$db->query( "DROP TABLE IF EXISTS `$db->Landmark`" );
	}

    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $newVersion = $args['new_version'];
        $db = $this->_db;

        if ($oldVersion < '1.4') {

            $sql = "ALTER TABLE `$db->Landmark` ADD COLUMN `postscript_text` text collate utf8_unicode_ci default NULL";
            $db->query($sql);

            $sql = "ALTER TABLE `$db->Landmark` ADD COLUMN `landmark_image` text collate utf8_unicode_ci default NULL";
            $db->query($sql);
	    }
	}

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
            'controller' => 'landmarks-controller',
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
