<?php
	//echo $this->Html->script('/cake_grid/js/ColReorder/ColReorderWithResize',false);
	echo $this->Html->script('/cake_grid/js/jquery.dataTables',true);
	echo $this->Html->script('/cake_grid/js/ColVis/js/ColVis',true);
	echo $this->Html->script('/cake_grid/js/TableTools/TableTools',true);
	echo $this->Html->script('/cake_grid/js/TableTools/ZeroClipboard',true);
	$model = current($this->request->params['models']);
	$model = $model['className'];
	$tableId = $model.ucfirst($this->request->params['action']);
?>
<script type="text/javascript" charset="utf-8">
/* Note 'unshift' does not work in IE6. A simply array concatenation would. This is used
 * to give the custom type top priority
 */
jQuery.fn.dataTableExt.aTypes.unshift(
	function ( sData )
	{
		var sValidChars = "0123456789-,";
		var Char;
		var bDecimal = false;
		
		/* Check the numeric part */
		for ( i=0 ; i<sData.length ; i++ )
		{
			Char = sData.charAt(i);
			if (sValidChars.indexOf(Char) == -1)
			{
				return null;
			}
			
			/* Only allowed one decimal place... */
			if ( Char == "," )
			{
				if ( bDecimal )
				{
					return null;
				}
				bDecimal = true;
			}
		}
		
		return 'numeric-comma';
	}
);

/*jQuery.fn.dataTableExt.oSort['numeric-comma-asc']  = function(a,b) {
	var x = (a == "-") ? 0 : a.replace( /,/, "." );
	var y = (b == "-") ? 0 : b.replace( /,/, "." );
	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

jQuery.fn.dataTableExt.oSort['numeric-comma-desc'] = function(a,b) {
	var x = (a == "-") ? 0 : a.replace( /,/, "." );
	var y = (b == "-") ? 0 : b.replace( /,/, "." );
	x = parseFloat( x );
	y = parseFloat( y );
	return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};*/

$(document).ready(function() {
	$("#<?php echo $tableId; ?>").dataTable({
		"aLengthMenu": [[-1,2, 25, 50], ["All",2, 25, 50]],
		"iDisplayLength": 100,
		"bStateSave": false,
		"bSort": false,
        "oTableTools": {
		    "sSwfPath": "<?php echo $this->request->webroot; ?>cake_grid/DataTableMedia/TableTools/copy_csv_xls_pdf.swf",
		    "aButtons": [
		        "copy",
		        "xls",
		        {
		            "sExtends": 'pdf'
		        },
		        "print"
		    ]
		},
		"oColVis": {
			"activate": "mouseover",
			"sAlign": "right",
			"bRestore": true
		},
		"sDom": 'CTlfr<"clear">tip',
	});
	$('#<?php echo $tableId; ?>_paginate').hide(0);
	$('#OrderQuestionManage_filter input').val(null);
	
	$('.ajaxAction').click(function(event){
		if (event.preventDefault) {  // W3C variant
	        event.preventDefault()
	    } else { // IE<9 variant:
	        event.returnValue = false
	    }
		href = $(this).attr('href');
		var flag = true;
		if(href != '' && flag){
			flag = false;
			$.get(href,function(data){
				var obj = jQuery.parseJSON(data);

				$.each( obj, function( key, val ) {
					if(key == 'src'){
						$(this).children().attr('src',val);
					}
					else if (key == 'href'){
						$(this).attr('href',val);
					}
					else if (key == 'content'){
						$(this).val(val);
					}
					else if (key == 'message'){
						alert(val);
					}
				});
			});
			flag = true;
		}
	});
});
</script>
<div class="DataGrid">
	<table class="headerTable <?php echo $options['class_table'] ?>" id="<?php echo $tableId; ?>">
		<thead>
			<?php echo $headers ?>
		</thead>
	
		<tbody>
			<?php echo $results ?>
		</tbody>
	</table>

	<div class="Footer">
			<div id="MCakeGridInfo">
		 	<?php 
			 	echo $this->Paginator->counter('
			    	صفحه : {:page} از {:pages} , تعداد سطرها : {:current} از {:count},  شروع شده از رکورد {:start} تا  {:end} '
			    	); 
				?>
			</div>  
				<?php
				
				if ( $this->Paginator->hasPage(null,2) ){
					echo '<div id="MCakeGridPageNumber">',
					$this->Paginator->numbers(),
					'</div>';
					 
					echo $this->Html->image('/cake_grid/img/Horse-Chess-Crown-Logo.png',array('id' => 'pager')),
					$this->Paginator->prev('',array('class' => 'prev')),
					$this->Paginator->next('',array('class' => 'next'));
					  
					 /*echo '<div id="MCakeGridPageStep">';
					 echo $this->Paginator->first(' ابتدا' . ' | ' ); 
					 echo $this->Paginator->prev( __('صفحه قبل').' | ' , array(), null, array('class' => 'prev disabled')); 
					 echo $this->Paginator->next( __('صفحه بعد') .' | ' , array(), null, array('class' => 'prev disabled')); 
					 echo $this->Paginator->last('انتها');
					 echo '</div><div id="MCakeGridPageNumber">';
					 echo $this->Paginator->numbers();
					 echo '</div>';*/
				}
			?>  
	</div> 	
</div>