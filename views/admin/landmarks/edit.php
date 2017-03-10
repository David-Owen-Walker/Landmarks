<?php
$landmarkTitle = strip_formatting( landmark( 'title' ) );
if( $landmarkTitle != '' && $landmarkTitle != '[Untitled]' ) {
	$landmarkTitle = ': &quot;' . $landmarkTitle . '&quot; ';
} else {
	$landmarkTitle = '';
}
$landmarkTitle = 'Edit Landmark #' . landmark( 'id' ) . $landmarkTitle;

echo head( array( 'title' => $landmarkTitle, 'content_class' => 'vertical-nav',
		'bodyclass' => 'edit','bodyid'=>'landmark' ) );
include 'form-tabs.php';
echo flash();
?>

<form method="post" enctype="multipart/form-data" id="landmark-form" action="">
  <?php include "form.php" ?>

  <section class="three columns omega" id="landmark-editor-control-panel">
	<div id="save" class="panel">
	  <?php echo $this->formSubmit( 'submit', __('Save Changes'),
	array( 'id' => 'save-changes',
		'class' => 'submit big green button' ) ); ?>
      <a href="<?php echo html_escape( public_url( 'landmarks/show/' . $landmark->id ) ); ?>"
         class="big blue button" target="_blank">
        <?php echo __('View Public Page'); ?>
      </a>
      <?php echo link_to_landmark( __('Delete'),
	array( 'class' => 'delete-confirm big red button' ),
	'delete-confirm' ); ?>
    </div>




    <div id="public-featured">
      <?php if ( is_allowed('LandmarkBuilder_Landmarks', 'makePublic') ): ?>
      <div class="checkbox">
        <label for="public">
          <?php echo __('Public'); ?>:
        </label>
        <div class="checkbox">
          <?php echo $this->formCheckbox(
	'public', $landmark->public,
	array(), array( '1', '0' ) ); ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if( is_allowed( 'LandmarkBuilder_Landmarks', 'makeFeatured' ) ): ?>
      <div class="checkbox">
        <label for="featured">
          <?php echo __('Featured'); ?>:
        </label>
        <div class="checkbox">
          <?php echo $this->formCheckbox(
	'featured', $landmark->featured,
	array(), array( '1', '0' ) ); ?>
        </div>
      </div>
      <?php endif; ?>


    </div>
    
    <?php //if($landmark->hasImage('square_thumbnails')) {echo $landmark->square_thumbnail();} ?>

  </section>

</form>

<?php echo foot(); ?>
