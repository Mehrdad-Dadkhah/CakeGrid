<tr class="<?php echo $options['class_header'] ?>">
	<?php foreach($headers as $header):
	   $width = (isset($header['options']['width'])) ? 'width="' . $header['options']['width'] . '"' : "";
	   $width = (isset($header['options']['width'])) ? 'width="' . $header['options']['width'] . '"' : "";
	   if(!is_array($header['valuePath'])){
		   $header['valuePath'] = substr( $header['valuePath'],1);
		   $header['valuePath'] = str_replace('/','.',$header['valuePath']);
	   	   $temp = explode('.',$header['valuePath']);
	       $isSortable = false; 
		    if ( ( count($temp) == 2 ) and ( isset($this->request->params['paging'][$temp[0]] ) )){
		        $isSortable = true;
	        }
	   }
      if (isset($isSortable) && $isSortable == true):  
    ?>
	<th <?php echo $width ?> ><?php echo $header['title'] == 'Actions' ? 'عملیات' : $this -> Paginator -> sort($header['valuePath'], $header['title']); ?></th>
    <?php else: ?>
	<th <?php echo $width ?> ><?php echo $header['title'] == 'Actions' ? 'عملیات' : $header['title'] ?></th>
    <?php endif; ?>
	<?php endforeach; ?>
</tr>