<?php
$pageTitle = __('Browse Landmarks') . ' ' . __('(%s total)', $total_results );
$editable = is_allowed( 'Landmarks', 'edit' );
$addUrl = url( array( 'action' => 'add' ) );

echo head( array( 'title' => $pageTitle, 'bodyid'=>'landmark','bodyclass' => 'landmarks browse' ) );
echo flash();
?>

<?php if( $total_results ): ?>

<div class="table-actions">
  <?php if( is_allowed( 'Landmarks', 'add' ) ): ?>
  <a class="add button small green" href="<?php echo $addUrl; ?>">
    <?php echo __('Add a Landmark'); ?>
  </a>
  <?php endif; ?>
</div>

<div id="primary">
		<?php
		echo flash();
		if( has_landmarks() ):
		?>
		<div class="pagination"><?php echo pagination_links(); ?></div>
		<?php if( has_landmarks_for_loop() ): ?>
		 <table id="landmarks" class="simple" cellspacing="0" cellpadding="0">
		    <thead>
		       <tr>
		          <th scope="col">ID</th>
		          <th scope="col">Title</th>
		          <?php if( $editable ): ?>
		          <th scope="col">Edit?</th>
		          <?php endif; ?>
		       </tr>
		    </thead>
		    <tbody>
               
               <?php $key = 0;

				foreach( $landmarks as $landmark ):
					$oddness = ((++$key % 2) == 1) ? 'odd' : 'even';
					$showUrl = url( array( 'action' => 'show','id' => $landmark->id ), 'landmarkAction' );
					$editUrl = url( array( 'action' => 'edit','id' => $landmark->id ), 'landmarkAction' );
				?>
               
               <tr class="landmarks <?php echo $oddness; ?>">
                  <td scope="row"><?php echo $landmark->id; ?></td>
                  <td scope="row" <?php echo ($landmark->featured) ? 'class="featured"' : null?>>
                    <a href="<?php echo $showUrl; ?>">
                      <?php echo $landmark->title; ?>
                    </a>
                    <?php echo '<div class="admin-landmark-browse-meta"><strong>Locations</strong>: '.count($landmark->Items).( metadata( $landmark, 'Credits' ) ? ' &middot; <strong>Credits</strong>: '.metadata( $landmark, 'Credits' ).'' : null ).'<br><strong>Public</strong>: '.( (metadata( $landmark, 'Public' ) == 1) ? 'Yes' : 'No' ).' &middot; <strong>Featured</strong>: '.( (metadata( $landmark, 'Featured' ) == 1) ? 'Yes' : 'No' ).'<div>';?>

                    <?php // if( $landmark->hasImage() ){ echo '<i class="fa fa-camera"></i>';} ?>

                  </td>
                  <?php if( $editable ): ?>
                  <td>
                    <a class="edit" href="<?php echo $editUrl; ?>">
                      <?php echo __('Edit'); ?>
                    </a>
                  </td>
                  <?php endif; ?>
               </tr>
               <?php endforeach; ?>
            </tbody>
         </table>
      <?php endif; ?>
   <?php endif; ?>
</div>

<?php else: ?>

  <?php if( total_records( 'Landmark' ) === 0 ): ?>
    <h2><?php echo __('You have no landmarks.'); ?></h2>
    <?php if( is_allowed( 'Landmarks', 'add' ) ): ?>
    <p><?php echo __('Get started by adding your first landmark.'); ?></p>
    <a class="add big green button" href="<?php echo $addUrl; ?>">
      <?php echo __('Add a Landmark'); ?>
    </a>
    <?php endif; ?>
  <?php else: ?>
    <p><?php echo __('The query searched %s landmarks and returned no results.',
		total_records( 'Landmark' ));
echo __('Would you like to %s?',
	link_to_landmark_search( __('refine your search') ) ); ?></p>
  <?php endif; ?>

<?php endif; ?>

<?php echo foot(); ?>
