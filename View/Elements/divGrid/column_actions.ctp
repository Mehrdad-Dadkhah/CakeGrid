<?php
foreach($actions as $title => $action){
	#show status management icons ..............
	if($title == 'status'){
		$expURL = explode('/',$action['url']);
		$status = array_pop($expURL);
		$id = array_pop($expURL);
		array_push($expURL,$status,$id);
		$url = implode('/',$expURL);
		echo '<div class="statusManager" id="changeStatus'. $id .'">';
        if($status == 0){
            echo $this->Html->image(
            	'CakeGrid.tick.png',
            	array(
            		'title'=>__('تایید'),
            		'class'=>'confirmation',
            		'id' => $id . '/' . $status,
					'url' => $url
				)
			);
        }
        else{
            echo $this->Html->image(
            	'CakeGrid.stop.png',
            	array(
            		'title'=>__('رد'),
            		'class'=>'confirmation',
            		'id' => $id . '/' . $status,
					'url' => $url
				)
			);
        }
        echo '</div>';
	}
	elseif(empty($action['elementData'])){
		if ( isset($action['options']['type']) and $action['options']['type'] == 'postLink') {
	        unset( $action['options']['type']);
	   	    echo $this->Form->postLink($title, $action['url'], $action['options'] + array('class' => 'button'));
	
	    }else
	    {
	        echo $this->Html->link($title, $action['url'], $action['options'] + array('class' => 'button')); 
	    }
	}
	else{
		echo $this->element($action['options']['gridType'] . DS . $action['options']['element'],array('rowData' => $action['elementData']));
	}
}
?>