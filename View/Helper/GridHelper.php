<?php
class GridHelper extends AppHelper {
	public $name = 'Grid';
	public $plugin_name = 'CakeGrid';
	
	/**
	 * Load html helper for links and such
	 *
	 * @var string
	 */
	var $helpers = array('Html','CakeGrid.Tree' => array('className' => 'CakeGrid.TreeToGrid'));
	
	/**
	 * all Data
	 *
	 * @var array
	 */
	private $__allData = array();
	
	/**
	 * Settings for html classes and such
	 *
	 * @var string
	 */
	private $__settings = array();
	
	/**
	 * THe columns for the grid
	 *
	 * @var string
	 */
	private $__columns  = array();
	
	/**
	 * Actions column (if any)
	 *
	 * @var string
	 */
	private $__actions  = array();
	
	/**
	 * Totals for columns (if any)
	 *
	 * @var string
	 */
	private $__totals   = array();
	
	/**
	 * Directory where the grid elements are located. Makes for easy switching around.
	 * Out of the box we'll support table and csv.
	 *
	 * @var string
	 */
	var $elemDir;
	
	/*
	 * titleSlug for mainHeader in mainColumn in divGrid type ...
	 * 
	 * @var string 
	 */
	 var $mainTitleSlug;
	 
	 /*
	  * tree options when use treeGrid type
	  * 
	  */
	 private $__TreeOptions = array();
	
	/**
	 * Settings setup
	 *
	 * @author Robert Ross
	 */
	function __construct(View $View, $settings = array()){
	     parent::__construct( $View,$settings);
		$this->options(array());
	}
	
	/**
	 * Set options for headers and such
	 *
	 * @param string $options 
	 * @return void
	 * @author Robert Ross
	 */
	function options($options){
		$defaults = array(
			'class_header'  => 'cg_header',
			'class_colGroup'  => 'cg_colGroup',
			'class_row'     => 'cg_row',
			'class_table'   => 'cg_table',
			'empty_message' => __('No Results'),
			'separator'     => ' ',
			'type'          => 'table'
		);
		
		$options = array_merge($defaults, $options);
		
		$this->__settings = $options;
		
		//-- Set the directory we'll be looking for elements
		$this->elemDir = $this->__settings['type'];
	}
	
	/**
	 * Resets columns and actions so multiple grids may be created
	 *
	 * @return void
	 * @author Robert Ross
	 */
	function reset(){
		$this->__columns = array();
		$this->__actions = array();
	}
	
	/**
	 * Adds a column to the grid
	 *
	 * @param string $title 
	 * @param string $valuePath 
	 * @param array $options 
	 * @return void
	 * @author Robert Ross
	 */
	function addColumn($title, $valuePath, array $options = array()){
		$defaults = array(
			'editable' => false,
			'type' 	   => 'string',
			'element'  => false,
			'linkable' => false,
			'total'    => false,
			'emptyVal' => null
		);
		
		$options = array_merge($defaults, $options);
		
		$titleSlug = Inflector::slug($title);
		$this->__columns[$titleSlug] = array(
			'title'     => $title,
			'valuePath' => $valuePath,
			'options'   => $options
		);
		
		if($options['total'] == true){
			$this->__totals[$title] = 0;
		}
		
		return $titleSlug;
	}
	
	/**
	 * Adds an actions column if it doesnt exist, then creates 
	 *
	 * @param string $name 
	 * @param array $url 
	 * @param array $trailingParams - This is the stuff after /controller/action. Such as /orders/edit/{id}. It's the action parameters in other words
	 * @return void
	 * @author Robert Ross
	 */
	function addAction($name, array $url, array $trailingParams = array(), array $options = array()){
		$this->__actions[$name] = array(
			'url'  			 => $url,
			'trailingParams' => $trailingParams,
			'options'        => $options
		);
		if(!empty($options['elementData'])){
			$this->__actions[$name]['options']['gridType'] = $this->__settings['type'];
		}

		if(!isset($this->__columns['actions'])){
			$this->addColumn('Actions', null, array('type' => 'actions'));
		}
		
		return true;
	}
	
	/**
	 * Adds a column to the grid for show status of each items
	 *
	 * @param string $title 
	 * @param string $valuePath 
	 * @param array $options 
	 * @return void
	 * @author Mehrdad Dadkhah
	 */
	function addStatusManage($title = 'status', array $options = array()){
		$View = $this->_View();
		$this->Html->script('CakeGrid.changeStatus',array('inline' => false));
		
		$defaults = array(
			'editable' => false,
			'type' 	   => 'string',
			'element'  => false,
			'linkable' => false,
			'total'    => false,
			'status'   => true,
			'fieldName' => 'status',
			'primaryKey' => 'id'
		);
		$options = array_merge($defaults, $options);
		
		$titleSlug = Inflector::slug($title);

		$models = current($View->request->params['models']);
		$curentModel = $models['className'];
		$valuePath = '/' . $curentModel . '/' . $options['fieldName'];
		
		$this->__columns[$titleSlug] = array(
			'title'     => $title,
			'valuePath' => $valuePath,
			'options'   => $options
		);
		
		if($options['total'] == true){
			$this->__totals[$title] = 0;
		}
		
		#make status action
		$name = 'status';
		$url = array(
			'controller' => $View->request->params['controller'],
			'action' => 'changeStatus'
		);
		$trailingParams = array(
			'/' . $curentModel . '/' . $options['primaryKey'],
			'/' . $curentModel . '/' . $options['fieldName']
		);
		$options = array('escape'=> false);
		if($this->mainTitleSlug == null){
			$this->addAction($name, $url, $trailingParams, $options);
		}
		else{
			$this->addCommonAction($name, $url, $trailingParams, $options);
		}
		return $titleSlug;
	}
	
	/**
	 * Adds a common value to the grid for show status of each items
	 *
	 * @param string $title 
	 * @param string $valuePath 
	 * @param array $options 
	 * @return void
	 * @author Mehrdad Dadkhah
	 */
	function addCommonStatusManage(/*$title = 'status',*/ array $options = array()){
		$title = 'status';
		$View = $this->_View();
		$this->Html->script('CakeGrid.changeStatus',array('inline' => false));
		
		$defaults = array(
			'editable' => false,
			'type' 	   => 'string',
			'element'  => false,
			'linkable' => false,
			'total'    => false,
			'status'   => true,
			'fieldName' => 'status',
			'primaryKey' => 'id'
		);
		$options = array_merge($defaults, $options);

		$models = current($View->request->params['models']);
		$curentModel = $models['className'];
		$valuePath['/' . $curentModel . '/' . $options['fieldName']] = $title;
		$valuePath['type'] = 'commonValue';
		$valuePath['separator'] = $options['separator'];
		$oldValuePath = isset($this->__columns[$this->mainTitleSlug]['commonValue']) && $this->__columns[$this->mainTitleSlug]['commonValue']['valuePath'] != null ? $this->__columns[$this->mainTitleSlug]['commonValue']['valuePath'] : array();
		$vPath = array_merge($oldValuePath,$valuePath);
		$this->__columns[$this->mainTitleSlug]['commonValue']['valuePath'] = $vPath;
		
		#make status action
		$name = 'status';
		$url = array(
			'controller' => $View->request->params['controller'],
			'action' => 'changeStatus'
		);
		$trailingParams = array(
			'/' . $curentModel . '/' . $options['primaryKey'],
			'/' . $curentModel . '/' . $options['fieldName']
		);
		$options = array('escape'=> false);
		if($this->mainTitleSlug == null){
			$this->addAction($name, $url, $trailingParams, $options);
		}
		else{
			$this->addCommonAction($name, $url, $trailingParams, $options);
		}
		return $this->mainTitleSlug;
	}

	/**
	 * create center column as a main column
	 *
	 * @param string $header 
	 * @return void
	 * @author Mehrdad Dadkhah
	 */
	 function addMainHeader($title, $valuePath = null, array $options = array()){
	 	$defaults = array(
			'editable' => false,
			'type' 	   => 'mainColumn',
			'element'  => false,
			'linkable' => false,
			'total'    => false,
			'tree' => false
		);
		
		$options = array_merge($defaults, $options);
		
		$this->mainTitleSlug = Inflector::slug($title);
		if($this->mainTitleSlug == null){
			$this->mainTitleSlug = $title;
		}

		$this->__columns[$this->mainTitleSlug] = array(
			'title'     => $title,
			'valuePath' => $valuePath,
			'options'   => $options
		);
		
		if($options['total'] == true){
			$this->__totals[$title] = 0;
		}
		
		return $this->mainTitleSlug;
	 }
	/*
	 * Adds main value to mainHeader column with mainValue type if it doesnt exist
	 * 
	 * @param string $title 
	 * @param string $valuePath 
	 * @param array $options 
	 * @return void
	 * @author Mehrdad Dadkhah
	 */
	 function addMainValue($title, $valuePath, array $options = array()){
		$oldValuePath = isset($this->__columns[$this->mainTitleSlug]['mainValue']) && $this->__columns[$this->mainTitleSlug]['mainValue']['valuePath'] != null ? $this->__columns[$this->mainTitleSlug]['mainlValue']['valuePath'] : array();
		$newValuePath = array();
		if(!is_array($valuePath)){
			$newValuePath = array(
				$valuePath => $title
			);
		}
		elseif (is_array($valuePath)) {
			$newValuePath['separator'] = $valuePath['separator'];
			unset($valuePath['separator']);
			unset($valuePath['type']);
			foreach ($valuePath as $key => $value) {
				$newValuePath[$key] = $value;
			}
		}
		
		$vPath = array_merge($oldValuePath,$newValuePath);
		$vPath['type'] = 'mainValue';
		$this->__columns[$this->mainTitleSlug]['mainValue']['valuePath'] = $vPath;
	 }
	 
	 /*
	 * create center column as a main column
	 * 
	 * @param string $title 
	 * @param string $valuePath 
	 * @param array $options 
	 * @return void
	 * @author Mehrdad Dadkhah
	 */
	 function addCommonValue($title, $valuePath, array $options = array()){
	 	$oldValuePath = isset($this->__columns[$this->mainTitleSlug]['commonValue']) && $this->__columns[$this->mainTitleSlug]['commonValue']['valuePath'] != null ? $this->__columns[$this->mainTitleSlug]['commonValue']['valuePath'] : array();
		$newValuePath = array();
		if(!is_array($valuePath)){
			$newValuePath[$valuePath] = $title;
		}
		elseif (is_array($valuePath)) {
			if(isset($valuePath['separator'])){
				$newValuePath['selfSeparator'] = $valuePath['separator'];
				unset($valuePath['separator']);
			}			
			if(isset($valuePath['type'])){				
				if($valuePath['type'] == 'concat'){
					unset($valuePath['type']);
					$firstVal = null;
					foreach ($valuePath as $key => $value) {
						if($firstVal == null){
							$firstVal = $value;
							continue;
						}
						$newValuePath[$firstVal]['concatWith'][] = $value;
					}
					$newValuePath[$firstVal]['title'] = $title;
				}
			}
					
		}
		if(isset($options['element'])){
			$this->__columns[$this->mainTitleSlug]['options']['element'][$valuePath] = $options['element'];
		}
		
		$vPath = array_merge($oldValuePath,$newValuePath);
		$vPath['type'] = isset($options['type'])&&$options['type'] == 'actions' ? 'commonAction' : 'commonValue';
		$this->__columns[$this->mainTitleSlug]['commonValue']['valuePath'] = $vPath;
		if(isset($options['readableVal'])){
			$this->__columns[$this->mainTitleSlug]['options'][$valuePath]['readableVal'] = $options['readableVal'];
		}
		if(isset($options['emptyVal'])){
			$this->__columns[$this->mainTitleSlug]['options'][$valuePath]['emptyVal'] = $options['emptyVal'];
		}
	 }
	 
	 /**
	 * Adds an actions column if it doesnt exist, then creates 
	 *
	 * @param string $name 
	 * @param array $url 
	 * @param array $trailingParams - This is the stuff after /controller/action. Such as /orders/edit/{id}. It's the action parameters in other words
	 * @return void
	 * @author Mehrdad Dadkhah
	 */
	function addCommonAction($name, array $url, array $trailingParams = array(), array $options = array()){
		$this->__actions[$name] = array(
			'url'  			 => $url,
			'trailingParams' => $trailingParams,
			'options'        => $options
		);
		if(!empty($options['elementData'])){
			$this->__actions[$name]['options']['gridType'] = $this->__settings['type'];
		}
		
		if(!isset($this->__columns['actions'])){
			$this->addCommonValue('Actions',implode('/',$url), array('type' => 'actions'));
		}
		
		return true;
	}
	
	/**
	 * set $this->__TreeOtions for use in Tree helper and add column with tree type
	 *
	 * @param string $element
	 * @param array $options 
	 * @return void
	 * @author Mehrdad Dadkhah
	 */
	 function addTree($element,$options = array()){
	 	$this->__TreeOptions = array_merge(array(
			'model' => null,
			'alias' => 'name',
			'type' => 'div',
			'itemType' => 'div',
			'id' => 'treeGrid',
			'class' => 'MTree',
			'element' => $element,
			'callback' => false,
			'autoPath' => false,
			'left' => 'lft',
			'right' => 'rght',
			'depth' => 0,
			'firstChild' => true,
			'indent' => null,
			'splitDepth' => false,
			'splitCount' => 3
		), (array)$options);
	 }
	 
	/**
	 * Generates the entire grid including headers and results
	 *
	 * @param string $results 
	 * @return void
	 * @author Robert Ross
	 */
	function generate($results){
		$this->__allData = $results;
		$View = $this->_View();
		if($this->__settings['type'] == 'table'){
        	$this->Html->css('CakeGrid.cakegrid',null,array('inline' => false));
		}
		elseif($this->__settings['type'] == 'divGrid'){
        	$this->Html->css('CakeGrid.divGrid',null,array('inline' => false));
		}
        $this->Html->script('CakeGrid.cakegrid',array('inline' => false));
		$directory = $this->__settings['type'];
		
		if($this->__settings['type'] == 'csv' && !empty($this->__totals)){
			array_unshift($this->__columns, array(
				'title' => '',
				'valuePath' => '',
				'options' => array(
					'type' => 'empty'
				)
			));
		}
		
		//-- Build the columns
		$headers = $View->element($this->elemDir . DS . 'grid_headers', array(
			'plugin'  => $this->plugin_name, 
			'headers' => $this->__columns,
			'options' => $this->__settings
		),
        array(			'plugin'  => $this->plugin_name)
        );
	
//		$colGroup = $View->element($this->elemDir . DS . 'grid_colGroup', array(
//			'plugin'  => $this->plugin_name, 
//			'headers' => $this->__columns,
//			'options' => $this->__settings
//		),
//        array(			'plugin'  => $this->plugin_name)
//        );

        $results = $this->results($results);
		$generated = $View->element($this->elemDir . DS . 'grid_full', array(
         //   'plugin'  => $this->plugin_name,
			'headers' => $headers,
         //   'colGroup' => $colGroup,
			'results' => $results,
			'options' => $this->__settings
		),
        array(	'plugin'  => $this->plugin_name)
        );
      
    	return $generated;
        
	}
	
	/**
	 * Creates the result set inclusive of the actions column (if applied)
	 *
	 * @param string $results 
	 * @return void
	 * @author Robert Ross
	 */
	function results($results = array()){
		$rows = array();
		$View = $this->_View();
		//$View->Helpers->load('CakeGrid.Tree');
		//App::import('TreeHelper','Helper');
		foreach($results as $key => $result){
			//-- Loop through columns
			$rowColumns = array();
			foreach($this->__columns as $column){
				if(isset($column['options']['tree']) && $column['options']['tree'] == true){
					$model = current($View->request->params['models']);
					$model = $model['className'];
					if(isset($result['Child'.$model])){
						$result['children'] = $result['Child'.$model];
					}
					elseif (isset($result['child'.$model])) {
						$result['children'] = $result['children'.$model];
					}
					elseif (isset($result['Children'.$model])) {
						$result['children'] = $result['Children'.$model];
					}
					elseif (isset($result['children'.$model])) {
						$result['children'] = $result['children'.$model];
					}

					$treeData[0] = $result;
					#make actions ...........................................................
					$View = $this->_View();
					$actions = array();
					//-- Need to retrieve the results of the trailing params
					foreach($this->__actions as $name => $action){
						//-- Check to see if the action is supposed to be hidden for this result (set in the controller)
						if(isset($result['show_actions']) && is_array($result['show_actions']) && !in_array($name, $result['show_actions'])){
							continue;
						}
						
						//-- Need to find the trailing parameters (id, action type, etc)
						$trailingParams = array();
						if(!empty($action['trailingParams'])){
							foreach($action['trailingParams'] as $key => $param){
								$tempp = Set::extract($param, $result);
								$trailingParams[$key] = array_pop($tempp);
							}
						}
						$actions[$name] = array(
							'url' => Router::url($action['url'] + $trailingParams,true),
							'options' => $action['options']
						);
					}
					$this->__TreeOptions['actions'] = $actions;
					$temp['row'] = $this->Tree->generate($treeData,$this->__TreeOptions);
					$temp['isSearchableData'] = 0;
					$rowColumns[] = $temp;
				}
				else{
					if(!isset($column['mainValue']) && !isset($column['commonValue'])){
						$temp['isSearchableData'] = isset($column['valuePath']['type'])&&$column['valuePath']['type'] == 'div' || isset($column['valuePath']['type'])&&$column['valuePath']['type'] == 'commonAction' || isset($column['valuePath']['type'])&&$column['valuePath']['type'] == 'commonValue' || isset($column['valuePath']['searchable'])&&$column['valuePath']['searchable']==false || $column['title'] == 'Actions' ? 0 : 1;
						if(isset($column['options']['status'])){
							$temp['statusRow'] = true;
							
							$View = $this->_View();
							$models = current($View->request->params['models']);
							$curentModel = $models['className'];
							$tempp = Set::extract('/' . $curentModel . '/' . $column['options']['primaryKey'], $result);
							$temp['id'] = array_pop($tempp);
						}
						$temp['row'] = $this->__generateColumn($result, $column);
					}
					else{
						if(isset($column['mainValue'])){
							$temp['isSearchableData'] = 0;
							$thisColumn = $column['mainValue'];
							$thisColumn['options'] = $column['options'];
							$thisColumn['title'] = $column['title'];
							if(isset($column['options']['status'])){
								$temp['statusRow'] = true;
								
								$View = $this->_View();
								$models = current($View->request->params['models']);
								$curentModel = $models['className'];
								$tempp = Set::extract('/' . $curentModel . '/' . $column['options']['primaryKey'], $result);
								$temp['id'] = array_pop($tempp);
							}
							$temp['row'] = $this->__generateColumn($result, $thisColumn);
						}
						if(isset($column['commonValue'])){
							$temp['isSearchableData'] = 0;
							$thisColumn = $column['commonValue'];
							$thisColumn['options'] = $column['options'];
							$thisColumn['title'] = $column['title'];
							if(isset($column['options']['status'])){
								$temp['statusRow'] = true;
								
								$View = $this->_View();
								$models = current($View->request->params['models']);
								$curentModel = $models['className'];
								$tempp = Set::extract('/' . $curentModel . '/' . $column['options']['primaryKey'], $result);
								$temp['id'] = array_pop($tempp);
							}
							$temp['row'] .= $this->__generateColumn($result, $thisColumn);
						}
					}
					$rowColumns[] = $temp;
				}
			}
			$rows[] = $View->element($this->elemDir . DS . 'grid_row', array(
				'plugin'     => $this->plugin_name, 
				'zebra'      => $key % 2 == 0 ? 'odd' : 'even', 
				'rowColumns' => $rowColumns,
				'options'    => $this->__settings,
				'recId'		 => $key,
			),
            array(	'plugin'  => $this->plugin_name)
            );
			
			if(!empty($this->__totals)){
				$totalColumns = array();
				
				$i = 0;
				foreach($this->__columns as $column){
					if($i == 0){
						$totalColumns[] = 'Total';
						$i++;
						continue;
					}
					$i++;
					
					if(isset($this->__totals[$column['title']])){
						if($column['options']['type'] == 'money'){
							$total = money_format("%n", $this->__totals[$column['title']]);
						} else if($column['options']['type'] == 'number'){
							$total = number_format($this->__totals[$column['title']]);
						}
						
						if($this->__settings['type'] == 'csv'){
							$total = floatval(str_replace(array('$', ','), '', $total));
							$totalColumns[] = $total;
							continue;
						}
						
						$totalColumns[] = $total . ' (total)';
						continue;
					}
					
					$totalColumns[] = '';
				}
				
				$rows[] = $View->element($this->elemDir . DS . 'grid_row', array(
					'plugin' 	 => $this->plugin_name,
					'rowColumns' => $totalColumns,
					'options'    => $this->__settings,
					'zebra'		 => 'totals'
				),
	            array(	'plugin'  => $this->plugin_name)
	            );
			}
			
		}
		//-- Upon review, this if statement is hilarious
		if(empty($rows) && !empty($this->__settings['empty_message'])){
			$rows[] = $View->element($this->elemDir . DS . 'grid_empty_row', array(
				'plugin' => $this->plugin_name,
				'colspan' => sizeof($this->__columns) + (sizeof($this->__actions) ? 1 : 0),
				'options'    => $this->__settings
			),
            array(	'plugin'  => $this->plugin_name));
		}
	   
		return implode("\n", $rows);
	}
	
	/**
	 * Creates the column based on the type. If there's no type, just a plain ol' string.
	 *
	 * @param string $result 
	 * @param string $column 
	 * @return void
	 * @author Robert Ross
	 */
	private function __generateColumn($result, $column){
		if($column['options']['type'] == 'empty'){
			return '';
		}
		
		if(!isset($column['valuePath'])){
			$value = $result;
		} else if(!is_array($column['valuePath'])) {
			$value = Set::extract($column['valuePath'], $result);
			$value = array_pop($value);
		} else if(is_array($column['valuePath'])){
			$valuePath = $column['valuePath'];
			
			if($valuePath['type'] == 'concat'){
				$separator = isset($valuePath['separator']) ? $valuePath['separator'] : $this->__settings['separator'];
				unset($valuePath['type'], $valuePath['separator']);
				
				$values = array();
				foreach($valuePath as $path){
					$extracted = Set::extract($path, $result);
					$values[] = array_pop($extracted);
				}
				
				$value = implode($separator, $values);
			} else if($valuePath['type'] == 'format'){
				$format = $valuePath['with'];
				unset($valuePath['type'], $valuePath['with']);
				
				$values = array($format);
				foreach($valuePath as $path){
					$extracted = (array) Set::extract($path, $result);
					$values[] = array_pop($extracted);
				}
				
				$value = call_user_func_array('sprintf', $values);
			} else if($valuePath['type'] == 'div'){
				$separator = isset($valuePath['separator']) ? $valuePath['separator'] : $this->__settings['separator'];
				unset($valuePath['type'], $valuePath['separator']);
				
				$values = array();
				foreach($valuePath as $path => $title){
					$extracted = Set::extract($path, $result);
					$path = explode('/',$path);
					$index = $path[1].ucfirst($path[2]);
					$values[$index] = $title.$separator.array_pop($extracted);
				}
				
				$value = '<div class="inRowDiv">';
				foreach ($values as $cls => $item) {
					$value .= '<div class="'.$cls.'">'.$item.'</div>';
				}
				$value .= '</div>';
			} else if($valuePath['type'] == 'mainValue'){
				$separator = isset($valuePath['separator']) ? $valuePath['separator'] : $this->__settings['separator'];
				unset($valuePath['type'], $valuePath['separator']);
				
				$values = array();
				foreach($valuePath as $path => $title){
					$extracted = Set::extract($path, $result);
					$path = explode('/',$path);
					$index = $path[1].ucfirst($path[2]);
					$values[$index] = $title.$separator.array_pop($extracted);
				}
				
				$value = '<div class="mainValue">';
				foreach ($values as $cls => $item) {
					$value .= '<div class="'.$cls.'">'.$item.'</div>';
				}
				$value .= '</div>';
			} else if($valuePath['type'] == 'commonValue' || $valuePath['type'] == 'commonAction'){
				$values = array();
				$divAction = null;
				if($valuePath['type'] == 'commonAction'){
					$View = $this->_View();
					$actions = array();
				
					//-- Need to retrieve the results of the trailing params
					foreach($this->__actions as $name => $action){
						//-- Check to see if the action is supposed to be hidden for this result (set in the controller)
						if(isset($result['show_actions']) && is_array($result['show_actions']) && !in_array($name, $result['show_actions'])){
							continue;
						}
						
						//-- Need to find the trailing parameters (id, action type, etc)
						$trailingParams = array();
						if(!empty($action['trailingParams'])){
							foreach($action['trailingParams'] as $key => $param){						
								$tempp = Set::extract($param, $result);
								$trailingParams[$key] = array_pop($tempp);
							}
						}
						$actions[$name] = array(
							'url' => Router::url($action['url'] + $trailingParams,true),
							'options' => $action['options']
						);
					}
					$divAction = $View->element($this->elemDir . DS . 'column_actions', array( 'actions' => $actions), array('plugin' => $this->plugin_name));
				}
				$separator = $this->__settings['separator'];
				$concatSeparator = null;
				if(isset($valuePath['selfSeparator'])){
					if(!is_array($valuePath)){
						$separator = $valuePath['selfSeparator'];
					}
					else {
						$concatSeparator = $valuePath['selfSeparator'];
					}
					unset($valuePath['type'], $valuePath['selfSeparator']);
				}
				elseif(isset($valuePath['separator'])){
					$separator = $valuePath['separator'];
				}
				
				unset($valuePath['type'], $valuePath['separator']);
				
				foreach($valuePath as $path => $title){
					if($title != 'Actions'){
						$extracted = Set::extract($path, $result);
						$path = explode('/',$path);
						if(isset($path[2])){
							$index = $path[1].ucfirst($path[2]);
						}
						else{
							$index = $path[1];
						}
						if($title == 'status'){
							$showStatus = array(
								0 => __('not confirmed'),
								1 => __('confirmed')
							);
							$values[$index] = $title.$separator.$showStatus[array_pop($extracted)];
						}
						else{
							$rowValue = array_pop($extracted);
							$vPath = implode('/',$path);
							if(is_array($column['options']['element']) && isset($column['options']['element'][$vPath])){
								$rowValue = $View->element('table' . DS .$column['options']['element'][$vPath], array('result' => $rowValue));
							}
							$path = implode('/',$path);
							if($rowValue != null && isset($column['options'][$path]) && !empty($column['options'][$path]['readableVal'])){
								$rowValue = $column['options'][$path]['readableVal'][$rowValue];
							}
							if($rowValue == null){
								if(isset($column['options'][$path]['emptyVal'])){
									$rowValue = $column['options'][$path]['emptyVal'];
								}
								else{
									$rowValue = $this->__settings['empty_message'];
								}
							}
							
							if(isset($column['valuePath'][$vPath]['concatWith'])){
								$val = $column['valuePath'][$vPath]['title'].$separator.$rowValue;
								foreach ($column['valuePath'][$vPath]['concatWith'] as $newPath) {									
									$path = $newPath;
									$extracted = Set::extract($path, $result);
									$rowValue = array_pop($extracted);
									if(is_array($column['options']['element']) && isset($column['options']['element'][$vPath])){
										$rowValue = $View->element('table' . DS .$column['options']['element'][$vPath], array('result' => $rowValue));
									}
									if($rowValue != null && isset($column['options'][$path]) && !empty($column['options'][$path]['readableVal'])){
										$rowValue = $column['options'][$path]['readableVal'][$rowValue];
									}
									if($rowValue == null){
										if(isset($column['options'][$path]['emptyVal'])){
											$rowValue = $column['options'][$path]['emptyVal'];
										}
										else{
											$rowValue = $this->__settings['empty_message'];
										}
									}
									if($concatSeparator  == null){
										$concatSeparator = ' ';
									}
									$val .= $concatSeparator . $rowValue;
									
								}
								$values[$index] = $val;
							}
							else{
								$values[$index] = $title.$separator.$rowValue;								
							}
						}
					}
				}
				$value = '<div class="commonValue">';
				foreach ($values as $cls => $item) {
					//if($column['options']['element'])
					$value .= '<div class="'.$cls.'">'.$item.'</div>';
				}				
				if($divAction != null){
					$value .= '<div class="manageActions">'.$divAction.'</div>';
				}
				$value .= '</div>';
			}
		}
		#change to readable value ......................
		if(isset($column['options']['readableVal']) && is_array($column['options']['readableVal'])){
			$value = $column['options']['readableVal'][$value];
		}
		#set empty message to emty row .................
		if($value == ''){
			if($column['options']['emptyVal'] != null){
				$value = $column['options']['emptyVal'];
			}
			else{
				$value = $this->__settings['empty_message'];
			}
		}
		//-- Total things up if needed
		if(isset($column['options']['total']) && $column['options']['total'] == true){
			$this->__totals[$column['title']] += $value;
		}
		
		if(isset($column['options']['element']) && $column['options']['element'] != false && !is_array($column['options']['element'])){
			$View = $this->_View();
            
			$column['options']['elementOtherValue'] = isset($column['options']['elementOtherValue']) ? $column['options']['elementOtherValue'] : null;				
			return  $View->element( $this->elemDir . DS .$column['options']['element'], array('elementOtherValue' => $column['options']['elementOtherValue'],'result' => $value ,'rowData' => $result,'options' => $column['options'],'col' => $column,'allData' => $this->__allData));
		} else {
			if(isset($column['options']['type']) && $column['options']['type'] == 'date'){
				$value = date('m/d/Y', strtotime($value));
			} else if(isset($column['options']['type']) && $column['options']['type'] == 'datetime'){
				$value = date('m/d/Y h:ia', strtotime($value));
			} else if(isset($column['options']['type']) && $column['options']['type'] == 'money' && $this->__settings['type'] != 'csv'){
				$value = money_format('%n', $value);
			} else if(isset($column['options']['type']) && $column['options']['type'] == 'actions'){
				$View = $this->_View();
				$actions = array();
			
				//-- Need to retrieve the results of the trailing params
				foreach($this->__actions as $name => $action){
					//-- Check to see if the action is supposed to be hidden for this result (set in the controller)
					if(isset($result['show_actions']) && is_array($result['show_actions']) && !in_array($name, $result['show_actions'])){
						continue;
					}
					
					//-- Need to find the trailing parameters (id, action type, etc)
					$trailingParams = array();
					if(!empty($action['trailingParams'])){
						foreach($action['trailingParams'] as $key => $param){
							if($param != 'null' && $param != null){
								$tempp = Set::extract($param, $result);
								$trailingParams[$key] = array_pop($tempp);
							}
							else{
								$trailingParams[$key] = 'null';
							}
						}
					}					
					$elementData = array();
					if(!empty($action['options']['elementData'])){
						foreach($action['options']['elementData'] as $key => $param){
							$tempp = Set::extract($param, $result);
							$elementData[$key] = array_pop($tempp);
						}
					}

					$actions[$name] = array(
						'url' => Router::url($action['url'] + $trailingParams,true),
						'options' => $action['options'],
						'elementData' => $elementData
					);
				}
			
				return $View->element($this->elemDir . DS . 'column_actions', array( 'actions' => $actions), array('plugin' => $this->plugin_name));
			}
		}
		//-- Check if it's linkable
		if(is_array($column['options']['linkable']) && !empty($column['options']['linkable'])){
			$trailingParams = array();
			
			$linkable = $column['options']['linkable'];
			if(!empty($linkable['trailingParams']) && is_array($linkable['trailingParams'])){
				foreach($linkable['trailingParams'] as $key => $param){
					$res = Set::extract($param, $result);
					$trailingParams[$key] = array_pop($res);
				}
			}
			
			$url = $linkable['url'] + $trailingParams;
			$linkable['options'] = !isset($linkable['options']) ? array() : $linkable['options'];
			
			$value = $this->Html->link($value, $url, $linkable['options']);
		}
		
		return $value;
	}
	
	/**
	 * Function to return escaped csv data since PHP doesn't have a function out of the box
	 * Taken from http://php.net/manual/en/function.fputcsv.php comment
	 *
	 * @param string $data 
	 * @return void
	 * @author Robert Ross
	 */
	function csvData($data){
		$fp  = false;
		$eol = "\n";
		
		if ($fp === false) {
		    $fp = fopen('php://temp', 'r+');
		} else {
		    rewind($fp);
		}

		if (fputcsv($fp, $data) === false) {
		    return false;
		}

		rewind($fp);
		$csv = fgets($fp);

		if ($eol != PHP_EOL){
		    $csv = substr($csv, 0, (0 - strlen(PHP_EOL))) . $eol;
		}
		
		//-- For out purpose... we don't want another \n
		$csv = substr($csv, 0, strlen($eol) * -1);
		
		return $csv;
	}
	
	/**
	 * Retrieves the view instance from the registry
	 *
	 * @return void
	 * @author Robert Ross
	 */
	private function _View() {
	   
       // $this->_View;
			//$View = ClassRegistry::getObject('');
		return $this->_View;
	}



}