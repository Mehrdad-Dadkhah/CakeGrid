<tr data-recId="<?= $recId?>" class="<?php echo $options['class_row'] ?> <?php echo $zebra; ?>">
	<?php 
		$showStatus = array(
			0 => __('not confirmed'),
			1 => __('confirmed')
		);
		foreach($rowColumns as $column):
	?>
	<td <?php if(isset($column['statusRow'])&&($column['row'] == '1' || $column['row'] == '0')) echo 'id="status'.$column['id'].'"'; ?> class="<?php echo $column['isSearchableData'] == 1 ? 'searchableGrid' : ''; ?>">
		<div class="data">
		<?php
			echo $column['isSearchableData'] == 1 ? '<div class="showTooltip"> double click to search with these value </div>' : '';
			if(
				isset($column['statusRow']) &&
				($column['row'] == '1' || $column['row'] == '0')
			){
				echo $showStatus[$column['row']];
			}
			else{
				echo $column['row'];
			}
		?>
		</div>
	</td>
	<?php endforeach; ?>
</tr>