<?php

/*
 * Helper functions
 */

function has_landmarks()
{
	return( total_landmarks() > 0 );
}

function has_landmarks_for_loop()
{
	$view = get_view();
	return $view->landmarks && count( $view->landmarks );
}


function landmark( $fieldName, $options=array(), $landmark=null )
{
	if( ! $landmark ) {
		$landmark = get_current_landmark();
	}

	switch( strtolower( $fieldName ) ) {
	case 'id':
		$text = $landmark->id;
		break;
	case 'title':
		$text = $landmark->title;
		break;
	case 'description':
		$text = $landmark->description;
		break;
	case 'credits':
		$text = $landmark->credits;
		break;
	case 'slug':
		$text = $landmark->slug;
		break;
	case 'postscript_text':
		$text = $landmark->postscript_text;
		break;
	case 'landmark_image':
		$text = $landmark->landmark_image;
		break;

	default:
		throw new Exception( "\"$fieldName\" does not exist for landmarks!" );
		break;
	}

	if( isset( $options['snippet'] ) ) {
		$text = snippet( $text, 0, (int)$options['snippet'] );
	}

	if( !is_array( $text ) ) {
		$text = html_escape( $text );
	} else {
		$text = array_map( 'html_escape', $text );

		if( isset( $options['delimiter'] ) ) {
			$text = join( (string) $options['delimiter'], (array) $text );
		}
	}

	return $text;
}

function set_current_landmark( $landmark )
{
	get_view()->landmark = $landmark;
}

function get_current_landmark()
{
	return get_view()->landmark;
}

function link_to_landmark(
	$text=null, $props=array(), $action='show', $landmarkObj = null )
{
	# Use the current landmark object if none given
	if( ! $landmarkObj ) {
		$landmarkObj = get_current_landmark();
	}

	# Create default text, if it was not passed in.
	if( empty( $text ) ) {
		$landmarkName = landmark('title', array(), $landmarkObj);
		$text = (! empty( $landmarkName )) ? $landmarkName : '[Untitled]';
	}

	return link_to($landmarkObj, $action, $text, $props);
}


function total_landmarks()
{
	$view = get_view();
	return count( $view->landmarks );
}

function nls2p($str) {
	$str = str_replace('<p></p>', '', '<p>'
		. preg_replace('#([
]\s*?[
]){2,}#', '</p><p>', $str)
		. '</p>');
	return $str;
}

function public_nav_landmarks( array $navArray = null, $maxDepth = 0 )
{
	if( !$navArray )
	{
		$navArray = array();

		$navArray[] = array(
			'label' => __('All'),
			'uri' => url('landmarks/browse') );

		/* TODO: Landmark Tags */

	}

	return nav( $navArray );
}

/*
** Display the thumb for the landmark.
** Used to generate slideshow, etc.
** TODO: expand $userDefined option to encompass either a user-set globally-defined img URL or a user-set landmark-specific img URL
** USAGE: display_landmark_thumb($this->landmark,0)
*/
function display_landmark_thumb($landmark,$i,$userDefined=null){

	$firstLandmarkItem=landmark_item_id($landmark,$i);

	$html='<div class="item-thumb hidden">';
	$html .= '<a href="'.html_escape(public_url('landmarks/show/'.landmark('id'))).'">';

	if($userDefined){
		$html .= '<img src="'.$userDefined.'"/>';

	}elseif($firstLandmarkItem){
		// use the thumb for the first item in the landmark
		$item = get_record_by_id('item', $firstLandmarkItem);
		$html .= item_image('square_thumbnail',array(),0,$item);

	}else{
		// use the fallback if their are no items in the landmark
		$html .= '<img src="'.public_url('plugins/LandmarkBuilder/views/public/images/default_thumbnail.png').'"/>';
	}

	$html .= '</a></div>';

	return $html;
}
/*
** Get an ID of an item in a landmark
** $landmark sets the landmark object
** $i is used to choose the position in the item array
** USAGE: landmark_item_id($this->landmark,0)
*/
function landmark_item_id($landmark,$i){
	$landmarkItems =array();
	foreach( $landmark->Items as $items ){
		array_push($landmarkItems,$items->id);
	}
	return isset($landmarkItems[$i]) ? $landmarkItems[$i] : null;
}

/*
** Uses the query parameters posted from the landmark location links on landmarks/show
** Adds a prev/info/next link to items/show for navigating landmark locations
*/

function landmark_nav( $html=null, $label='Landmark' )
{
	$intlLabel = __($label);

	if ( (isset($_GET['landmark'])) && (isset($_GET['index'])) )
	{
		$index = $_GET[ 'index' ];
		$landmark_id = $_GET['landmark'];
		$landmark = get_record_by_id( 'landmark', $landmark_id );

		$prevIndex = $index -1;
		$nextIndex = $index +1;

		$landmarkTitle = metadata( $landmark, 'title' );
		$landmarkURL = html_escape( public_url( 'landmarks/show/'.$landmark_id ) );

		// Items
		$current = landmark_item_id( $landmark, $index );
		$next = landmark_item_id( $landmark, $nextIndex );
		$prev = landmark_item_id( $landmark, $prevIndex );

		// Begin building the landmark navigation
		$html = ''
			. '<div class="landmark-nav">'
			. "$intlLabel " . __('navigation') . ':&nbsp;&nbsp;'
			. '<span id="landmark-nav-links">';

		// Add the previous item to the navigation if present.
		if( $prev )
		{
			$prevUrl = public_url( "items/show/$prev?landmark=$landmark_id&index=$prevIndex");
			$html .= ''
				. '<a title="' . __('Previous stop on %s', $intlLabel) .'"'
				. "href=\"$prevUrl\">" . __('Previous') . '</a>'
				. ' | ';
		}

		if( $landmarkURL )
		{
			$html .= '<a title= "'.__('View %1$s: %2$s', $intlLabel, $landmarkTitle).'"
         href="'.$landmarkURL.'">'.__('%s Info', $intlLabel).'</a>';
		}

		// Add the next item to the navigation if present
		if( $next )
		{
			$nextUrl = public_url( "items/show/$next?landmark=$landmark_id&index=$nextIndex");
			$html .= ' | '
				. '<a title="' . __('Next stop on %s', $intlLabel).'" href="'.$nextUrl.'">' . __('Next') . '</a>';
		}

		$html .= '</span>'
			. '<span id="close" style="cursor:pointer;"><span aria-hidden="true" class="icon-close"></span> <span style="display:none" class="text-close">Close</span></span>'
			. '</div>'

		// Add our necessary script element
		. '<script>'
			. 'jQuery( "span#close" ).click( function()'
			. '{'
			. '  jQuery( ".landmark-nav" ).fadeOut( "fast", "linear" );'
			. '});'
			. '</script>';

		return $html;
	}
}

/* get a list of related landmark links for a given item, for use on items/show template */
function landmarks_for_item($item_id=null,$heading=null){

	if(is_int($item_id)){
		$db = get_db();
		$prefix=$db->prefix;
		$select = $db->select()
		->from(array('ti' => $prefix.'landmark_items')) // SELECT * FROM omeka_landmark_items as ti
		->join(array('t' => $prefix.'landmarks'),    // INNER JOIN omeka_landmarks as t
			'ti.landmark_id = t.id')      // ON ti.landmark_id = t.id
		->where("item_id=$item_id AND public=1");      // WHERE item_id=$item_id
		$q = $select->query();
		$results = $q->fetchAll();

		$html=null;
		if($results){
			$h=(count($results)>1) ? __('Related Landmarks') : __('Related Landmark');
			$h = ($heading) ? $heading : $h;
			$html.='<div id="landmark-for-item"><h3>'.$h.'</h3><ul>';
			foreach($results as $result){
				$html.='<li><a class="landmark-for-item" href="/landmarks/show/'.$result['id'].'">';
				$html.=$result['title'];
				$html.='</a></li>';
			}
			$html.='</ul></div>';
		}
		return $html;
	}
}
