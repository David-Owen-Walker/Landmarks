<?php
$landmarkTitle = strip_formatting( landmark( 'title' ) );
if( $landmarkTitle != '' && $landmarkTitle != '[Untitled]' ) {
	$landmarkTitle = ': &quot;' . $landmarkTitle . '&quot; ';
} else {
	$landmarkTitle = '';
}
$landmarkTitle = 'Landmark #' . landmark( 'id' ) . $landmarkTitle;

echo head( array( 'title' => $landmarkTitle,
		'bodyclass' => 'show','bodyid'=>'landmark' ) );
echo flash();
?>

<section class="seven columns alpha">

  <?php if( metadata( 'landmark', 'Title' ) ): ?>
  <div id="landmark-title" class="element">
    <h2>Title</h2>
    <div class="element-text">
      <?php echo what_is_this_function( metadata( 'landmark', 'Title' ) ); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if( $landmark->slug ): ?>
  <div id="landmark-slug" class="element hidden">
    <h2>Slug</h2>
    <div class="element-text">
      <?php echo landmark( 'Slug' ); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if( metadata( 'landmark', 'Credits' ) ): ?>
  <div id="landmark-credits" class="element">
    <h2>Credits</h2>
    <div class="element-text">
      <?php echo metadata( 'landmark', 'Credits' ); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if( metadata( 'landmark', 'Description' ) ): ?>
  <div id="landmark-description" class="element">
    <h2>Description</h2>
    <div class="element-text">
      <?php echo what_is_this_function( metadata( 'landmark', 'Description' ) ); ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if( metadata( 'landmark', 'postscript_text' ) ): ?>
  <div id="postscript_text" class="element">
    <h2>Postscript Text</h2>
    <div class="element-text">
      <?php echo '<em>'.htmlspecialchars_decode(metadata( 'landmark', 'postscript_text' )).'</em>'; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if( metadata( 'landmark', 'landmark_image' ) ): ?>
<!--
  <div id="landmark_image" class="element">
    <h2>Landmark Image</h2>
    <div class="element-text">
      <?php echo '<img src="'.metadata( 'landmark', 'landmark_image' ).'" style="max-width:100%;height:auto;">'; ?>
    </div>
  </div>
-->
  <?php endif; ?>  
  
  <?php
$items = $landmark->getItems();
if( $landmark->getItems() ): ?>
  <div id="landmark-items" class="element">
    <h2>Items</h2>
    <div class="element-text">
      <ul>
        <?php foreach( $items as $item ):
		set_current_record( 'item', $item, true );
?>
        <li>
          <?php echo link_to_item(); ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <?php endif; ?>

</section>

<section class="three columns omega">
  <div id="edit" class="panel">
    <?php if( is_allowed( 'Landmarks', 'edit' ) ): ?>
    <a href="<?php echo url( array( 'action' => 'edit', 'id' => $landmark->id ) ); ?>"
       class="edit big green button">
      <?php echo __('Edit'); ?>
    </a>
    <?php endif; ?>

    <a href="<?php echo html_escape( public_url( 'landmarks/show/' . $landmark->id ) ); ?>"
       class="big blue button" target="_blank">
      <?php echo __('View Public Page'); ?>
    </a>

    <?php if( is_allowed( 'Landmarks', 'delete' ) ): ?>
    <?php echo link_to_landmark( __('Delete'),
		array( 'class' => 'big red button' ),
		'delete-confirm' ); ?>
    <?php endif; ?>
  </div>

  <div class="public-featured panel">
    <p>
      <span class="label">
        <?php echo __('Public'); ?>:
      </span>
      <?php echo ($landmark->public) ? __('Yes') : __('No'); ?>
    </p>
    <p>
      <span class="label">
        <?php echo __('Featured'); ?>:
      </span>
      <?php echo ($landmark->featured) ? __('Yes') : __('No'); ?>
    </p>
  </div>
</section>

<?php echo foot(); ?>
