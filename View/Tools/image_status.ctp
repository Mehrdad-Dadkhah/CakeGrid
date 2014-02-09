<?php
	if($status == 1){
        echo $this->Html->image('CakeGrid.tick.png',array('title'=>__('confirme'),'class'=>'confirmation','id' =>$id.'/'.$status));
    }
    elseif($status == 0){
        echo $this->Html->image('CakeGrid.stop.png',array('title'=>__('not confirmed'),'class'=>'confirmation','id' =>$id.'/'.$status));
    }