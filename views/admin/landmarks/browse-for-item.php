<?php
/*
$landmarkTitle = strip_formatting( landmark( 'title' ) );
if( $landmarkTitle != '' && $landmarkTitle != '[Untitled]' )
{
   $landmarkTitle = ': &quot;' . $landmarkTitle . '&quot; ';
}
else
{
   $landmarkTitle = '';
}
$landmarkTitle = 'Add Item To Landmark #' . landmark( 'id' ) . $landmarkTitle;

echo head( array( 'title' => $landmarkTitle,
                  'content_class' => 'vertical-nav',
                  'bodyclass' => 'landmarks primary' ) );
                  */

/*
?>
<div id="primary">
<table id="items" class="simple" cellspacing="0" cellpadding="0">
   <thead>
      <tr>
         <th scope="col">ID</th>
         <th scope="col">Item</th>
         <th scope="col">Add?</th>
      </tr>
   </thead>
   <tbody>
      <?php $key = 0;
            foreach( $this->items as $item ):
               $oddness = ((++ $key % 2) == 1 ) ? 'odd' : 'even';
               $itemName = metadata( $item, array( 'Dublin Core', 'Title' ) );
               $itemUrl = url( array( 'controller' => 'items',
                                      'action' => 'show',
                                      'id' => $item->id ), 'id' );
               $addUrl = url( array( 'action' => 'addItem',
                                     'item' => $item->id,
                                     'id' => $landmark->id ), 'landmarkItemAction' );
      ?>

      <tr class="items <?php echo $oddness; ?>">
         <td scope="row"><?php echo $item->id ?></td>
         <td scope="row">
            <a href="<?php echo $itemUrl; ?>">
              <?php echo $itemName; ?>
            </a>
         </td>
         <td scope="row">
            <a class="add" href="<?php echo $addUrl; ?>">
              <?php echo __('Add to landmark'); ?>
            </a>
         </td>
      </tr>
      <?php endforeach; ?>
   </tbody>
</table>
</div>
<?php
*/
//echo foot();
echo json_encode($this->items);
?>
