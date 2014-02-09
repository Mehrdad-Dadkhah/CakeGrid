      _____      _           _____      _     _ 
     / ____|    | |         / ____|    (_)   | |
    | |     __ _| | _____  | |  __ _ __ _  __| |
    | |    / _` | |/ / _ \ | | |_ | '__| |/ _` |
    | |___| (_| |   <  __/ | |__| | |  | | (_| |
     \_____\__,_|_|\_\___|  \_____|_|  |_|\__,_| 2.4


# CakeGrid 2.4

CakeGrid Plugin is DataGrid Tables for CakePHP  with some jquery facilities

thanks from zoghal(Mr Saleh Soozanchi)
   
## Requirements

* CakePHP 2.2.x+
* PHP5
 

## Installation
For CakePHP 1.3 support, please see the [1.3 branch](https://github.com/bobbytables/CakeGrid).


_[Manual]_

* Download this: [https://github.com/Mehrdad-Dadkhah/CakeGrid.git](https://github.com/Mehrdad-Dadkhah/CakeGrid.git)
* Unzip that download.
* Copy the resulting folder to `app/Plugin`
* Rename the folder you just copied to `Upload`

_[GIT Submodule]_

In your app directory type:

	git submodule add -b https://github.com/Mehrdad-Dadkhah/CakeGrid.git
	git submodule init
	git submodule update


[GIT Clone]_

In your `Plugin` directory type:

	git clone -b master https://github.com/Mehrdad-Dadkhah/CakeGrid.git

### Enable plugin

In 2.0 you need to enable the plugin your `app/Config/bootstrap.php` file:

	CakePlugin::load('CakeGrid');

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

## Usage

In your controller (or for global usage include it in your AppController) include the helper by adding this line:

	<?php
	class PagesController extends AppController {
	
    	public $helpers = array('CakeGrid.Grid');
	
		public function admin_index() {
	
			$this->set('Results', $this->paginate('Page') );
		
		}
	}
### just Grid
In your view file you can now create grids easily by doing something like this:

	<?php 
		$this->Grid->addColumn( __('شناسه'), "/Page/code",array('width' => '25%'));
		$this->Grid->addColumn( __('نام', "/Page/name",array('width' => '20%'));
		$this->Grid->addColumn( __('عنوان'), "/Page/title",array('width' => '20%'));
		$this->Grid->addColumn(__('وضعیت'), "/Page/status",array('width' => '10%' ,'element' => 'status'));//you can send data to element with "elementOtherValue" option

		$this->Grid->addAction(__('ویرایش'), array( 'controller' => 'posts' , 'action' => 'edit' ), array("/Page/id"));
		$this->Grid->addAction(__('حذف'), array('controller' => 'posts' ,  'action' => 'delete'), array("/Page/id"),array('confirm' => __('آیا مطمئن هستید؟'),'type' => 'postLink'));
		
		echo $this->Grid->generate($Results);
		    
	?>	
### Grid and div		
If you want have a div separator with special css and specify main value and other common value you can doing something like this:

	<?php
	 	$this->Grid->options(array('type' => 'divGrid'));
		$this->Grid->addColumn( __('شناسه'), "/Administrator/id",array('width' => '10%'));
		$this->Grid->addMainHeader(__('جزییات'));
		$this->Grid->addMainValue( __('نام'), "/Administrator/name");
		$this->Grid->addCommonValue( __('نام کاربری'), "/Administrator/user");
		$this->Grid->addCommonValue( __('ایمیل'), "/Administrator/email");
		$this->Grid->addCommonAction(__('ویرایش'), array( 'controller' => 'administrators' , 'action' => 'edit' ), array("/Administrator/id"),array('searchable' => false));
		$this->Grid->addCommonAction($this->Html->image('icons/delete.png',array('title'=>__('حذف کردن'))), array('controller' => 'administrators' ,  'action' => 'delete'), array("/Administrator/id"),array('confirm' => __('آیا مطمئن هستید؟'),'type' => 'postLink','escape' => false,'searchable' => false));
	
		echo $this->Grid->generate($Results);
	?>
	
## Grid and tree management

	<?php
	 	$this->Grid->options(array('type' => 'divGrid'));
		$this->Grid->addColumn( __('شناسه'), "/CommentQuestion/id",array('width' => '10%'));
		$this->Grid->addMainHeader(__('جزییات'),null,array('tree' => true));
		$this->Grid->addTree('tree_item');
		echo $this->Grid->generate($Results);
	?>
	tree_item is a element name that contain all thing you want have in tree management
	for example:
	<?php
		echo $this->Html->link($data['CommentQuestion']['member_id'], array( 
		    'controller' => 'users', 
		    'action' => 'view', 
		    'id' => $data['CommentQuestion']['member_id']));
		echo $this->Html->tag('div', h($data['CommentQuestion']['id']));     
		echo $this->Html->tag('div', h($data['CommentQuestion']['question_id']));     
		echo $this->Html->tag('div', h($data['CommentQuestion']['parent_id']));     
		echo $this->Html->tag('div', h($data['CommentQuestion']['content']));     
		echo $this->Html->tag('div', h($data['CommentQuestion']['uprate']));     
		echo $this->Html->tag('div', h($data['CommentQuestion']['downrate']));
	?>
	you have $data in the element that contain tree data
	
	in the second parameter in addTree function you can send options:
	     'model' => name of the model (key) to look for in the data array. defaults to the first model for the current
	  controller. If set to false 2d arrays will be allowed/expected.
	     'alias' => the array key to output for a simple ul (not used if element or callback is specified)
	     'type' => type of output defaults to ul
	     'itemType => type of item output default to li
	     'id' => id for top level 'type'
	     'class' => class for top level 'type'
	     'element' => path to an element to render to get node contents.
	     'callback' => callback to use to get node contents. e.g. array(&$anObject, 'methodName') or 'floatingMethod'
	     'autoPath' =>  array($left, $right [$classToAdd = 'active']) if set any item in the path will have the class $classToAdd added. MPTT only.
	     'left' => name of the 'lft' field if not lft. only applies to MPTT data
	     'right' => name of the 'rght' field if not lft. only applies to MPTT data
	     'depth' => used internally when running recursively, can be used to override the depth in either mode.
	     'firstChild' => used internally when running recursively.
	     'splitDepth' => if multiple "parallel" types are required, instead of one big type, nominate the depth to do so here
	         example: useful if you have 30 items to display, and you'd prefer they appeared in the source as 3 lists of 10 to be able to
	         style/float them.
	     'splitCount' => the number of "parallel" types. defaults to 3
 
This will create a 4 column grid (including actions) for all of your orders or whatever you like!
CakeGrid uses the Set::extract format found here: http://book.cakephp.org/view/1501/extract

If you're generating multiple tables per view, reset the grid and start over after you've generated your result set:

    $this->Grid->reset();
    
# Actions Column

    @param string $name 
    @param array $url 
    @param array $trailingParams
    @param array $options
    
	$this->Grid->addAction(
		__('حذف'), 
		array( 'controller' => 'posts' , 'action' => 'delete'), 
 		array("/Page/id"),
		array('confirm' => __('آیا مطمئن هستید؟'),'type' => 'postLink')
	);
		
    you can use element for actions:
    $this->Grid->addAction('',
		array(),
		array(),
		array('element' => 'front_menu_url','elementData' => array('/FrontsMenu/url'))
	);
	and in the element
	<?php
		$urlArray = explode('/',$rowData[0]);
		$url = 'http://'.$_SERVER['HTTP_HOST'].$rowData[0];
		if(in_array('http:',$urlArray)){                	
			$url = $rowData[0];
		}                	
		echo $this->Html->image('icons/world_link.png',
	        array(
	            'url' =>  $url
	        )
	    );
	 ?>
## What this does:

The First parameter if the link text (Edit, Delete, Rename, etc..)
The Second parameter is the controller action that will be handling the action.
The Third parameter is for the action parameters. So the id of the result, maybe a date? Whatever. Use your imagination.


# Advanced Functionality

CakeGrid allows you to make column results linkable. For example, if a column is for the order number, you can make the result a link to the actual order details.

For example:

    $this->Grid->addColumn('ID', '/Page/id', array('linkable' => array(
    	'url' => array('action' => 'details'),
    	'trailingParams' => array('/Page/id')
    )));
    
Linkable is the option parameter takes 3 sub options. url, trailingParams, and Html::link options (see http://book.cakephp.org/view/1442/link)

The url could be considered the controller and action, and maybe a named parameter. The trailing parameters is the id or whatever you like. It will be pulled from the result.
__Note:__ Named parameters are not yet supported, but so array('named' => array('id' => '/Page/id')) will not work, but array('id' => '/Page/id') will

## addStatusManagement
to have a column that show status of each records and have a ajax link to change status use this action
you should have a field with 0 or 1 data that show status
as default I set status for name of this column and id for primary key of table you can change it

just write this

<?php $this->Grid->addStatusManage(); ?>

in Advanced:

<?php $this->Grid->addStatusManage('article status',array('fieldName' => 'confirm','primaryKey' => 'key')); ?>

and then you should write a method in curent controller to change status with changeStatus name like:

<?php
	function changeStatus($status = null,$id = null){
	    $this->layout = 'ajax';
	    if($this->request->is('get')){
	            $this->Article->id=$id;
	            $newStatus = abs($status - 1);
	            $this->Article->saveField('status',$newStatus);
	    }
		$this->autoRender = false;
	}
?>

it's ok ...

## Total Row

To create a "totals" row. You can set a column to total. Only money and numbers will work (obviously).

The syntax is as follows:

    $this->addColumn('Amount', '/Page/amount', array('total' => true));
    
This will produce a final row with the totals on it for the column. If the column type is set to money or number, it will format the totals as well.

## Concat and Format

CakeGrid allows you to do concatenation and sprintf formatting on your cells. For example, if you have a first and last name but don't want to use CakePHP's virtualFields to merge them together, you can use CakeGrid to do it.

### Concat

    $this->Grid->addColumn('User', array(
    	'type' => 'concat', 
    	'/User/first_name',
    	'/User/last_name'
    ));
    
This will output in the cell the users first and last name together. Concat uses spaces as the default separator but can be changed in 2 ways.
    
    // Inline with the column options
    $this->Grid->addColumn('User', array(
    	'type' => 'concat', 
    	'separator' => ' ',
    	'/User/first_name',
    	'/User/last_name'
    ));
    
    // Global usage
    $this->Grid->options(array(
        'separator' => ' '
    ));
    
    $this->Grid->addCommonValue(__('شماره تماس ها'),array(
	    	'type' => 'concat', 
	    	'separator' => ' - ',
	    	'/SchoolInformation/phone1',
	    	'/SchoolInformation/phone2'
	    )
	);
    
### Formatting

    $this->Grid->addColumn('Register Date', array(
        'type' => 'format',
        'with' => '%s (%s)',
        '/User/created',
        '/User/register_ip'
    ));
### Div
	$this->Grid->addMainValue('detail', array(
    	'type' => 'div', 
    	'separator' => ' : ',
    	'/Administrator/name' => __('نام'),
    	'/Administrator/user' => __('نام کاربری')
    ));
###  Width 
		$this->Grid->addColumn( __('عنوان'), "/Page/title",array('width' => '20%'));

## Elements

CakeGrid allows the usage of your own elements to be used in cells. This is useful if you're wanting to use a hasMany relationship into a dropdown or something similar.
When using an element, a valuePath is not used. CakeGrid will pass the entire result of the row to the element.

For Example:

		$this->Grid->addColumn(__('وضعیت'), "/Page/status",array('width' => '10%' ,'element' => 'status'));    
Whatever the result is for the current row will get passed to the element as $result.

So in your element (/Elements/table/status.ctp for example)

		<?php 
			$i = ($result == true) ?  'icons/icon_accept.png' :  'icons/stop.png' ; ?>
			$link = (isset($options['link']))? $options['link'] : false;
		?>
		<center>
		    <?php echo $this->Html->image($i,array('data-link' =>$link, 'id' => '')); ?>
		</center>

## Element Other Value
	to send your value to colum's element
	
	$this->Grid->addColumn(__('بلوک راست'),'/blocks_sitelayouts/place',array('element' => 'block_counter','elementOtherValue' => array('place' => 0)));
	
	and in the block_counter element:
	
	<?php
		$cnt = 0;
	    foreach ($rowData['Block'] as $block){
	        if($block['blocks_sitelayouts']['place'] == $elementOtherValue['place'])
	            $cnt ++;
	    }
	    echo $this->Html->link($cnt,
	        array('controller' => 'blocks_sitelayouts','action' => 'JoinTogether',$rowData['Sitelayout']['id'],'right'),
	        array('title' => __('ویرایش'))
	    );
    ?>
    
    and we have some part of data like allData to have all return data to manage ...
    
## Empty fileds
if be a empty field in a record you can show a message with tow way ...
as default it shows "No Result"
you can set a global option for current table with:
<?php
	$this->options(
		'empty_message' => __('empty')
	);
?>

and can specify empty text for each column for example:

<?php
	$this->Grid->addColumn(__('سرآغاز'),'/ParentFrontsMenu/title',array('emptyVal' => __('بدون سرآغاز'))); 
?>

## Readable Value
if field value is int and you want to replace it to readable value can use this feature like:

<?php
	$fileType = array(
	    'عکس',
	    'صوت',
	    'فیلم'
	);
	$this->Grid->addColumn(__('نوع فایل'),'/GalleriesFile/type',array('readableVal' => $fileType));
?>

##License

 Copyright (c) 2011 The Daily Save, LLC.  All rights reserved.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.