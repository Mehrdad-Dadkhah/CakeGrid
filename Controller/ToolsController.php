<?php
	class Toolscontroller extends CakeGridAppController{
		/**
		 * just to use in ajax for load status images
		 *
		 * @param int $status
		 * @param int $id
		 * @return void
		 * @author Mehrdad Dadkhah
		 */
		 public function imageStatus($status,$id){
		 	$this->set('status',$status);
			$this->set('id',$id);
		 }
	 
	 
	}
