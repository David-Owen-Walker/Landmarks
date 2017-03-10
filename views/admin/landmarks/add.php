<?php
echo head( array( 'title' => 'Add Landmark', 'content_class' => 'vertical-nav',
		'bodyclass' => 'landmarks primary add-landmark-form' ) );
include 'form-tabs.php';
echo flash();
?>

<form method="post" enctype="multipart/form-data" id="landmark-form" action="">
  <?php include( 'form.php' ); ?>
  <section class="three columns omega">
    <div id="save" class="panel">
      <?php echo $this->formSubmit( 'submit', __('Add Landmark'),
	array( 'id' => 'save-changes',
		'class' => 'submit big green button' ) ); ?>
    </div>

    <div id="public-featured">
      <?php if( is_allowed( 'LandmarkBuilder_Landmarks', 'makePublic' ) ): ?>
      <div class="checkbox">
        <label for="public">
          <?php echo __('Public'); ?>:
        </label>
        <div class="checkbox">
          <?php echo $this->formCheckbox( 'public', $landmark->public,
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
          <?php echo $this->formCheckbox( 'featured', $landmark->featured,
	array(), array( '1', '0' ) ); ?>
        </div>
      </div>
      <?php endif; ?>

    </div>

  </section>

</form>

<?php echo foot(); ?>
