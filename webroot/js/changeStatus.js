$(function(){
	$('div.statusManager a').click(function(event){
		event.preventDefault();
	});
	$('.confirmation').click(function(){
		
        var href = $(this).parent().attr('href');            
        $.get(href,function(data){

                var parametr = href.split('/');
                id = parametr.pop();
                lastStatus = parametr.pop();

                if(lastStatus == '0'){                    
                    $('#status' + id + ' .data').html('تایید شده');
                    parametr.push('1');
                    parametr.push(id);
                    newHref = parametr.join('/');
                    $('div#changeStatus' + id + ' a').attr('href',newHref);
                    //$('div#changeStatus' + id + ' a').load(urlPrefix + '/cake_grid/img/stop.png');
                    $.get(urlPrefix + '/cake_grid/tools/imageStatus/' + lastStatus + '/' + id,function(data){
                    	$('div#changeStatus' + id + ' a').html(data);
                    });                    
                }
                else if(lastStatus == '1'){                      
                    $('#status' + id + ' .data').html('تایید نشده');
                    parametr.push('0');
                    parametr.push(id);
                    newHref = parametr.join('/');
                    $('div#changeStatus' + id + ' a').attr('href',newHref);
                    //$('div#changeStatus' + id + ' a').load(urlPrefix + '/cake_grid/img/tick.png'); 
                    $.get(urlPrefix + '/cake_grid/tools/imageStatus/' + lastStatus + '/' + id,function(data){
                    	$('div#changeStatus' + id + ' a').html(data);
                    });              
                }                     
        });
   });
   
   $('.confirmation').live('click', function(){
        var href = $(this).parent().attr('href');            
        $.get(href,function(data){

                var parametr = href.split('/');
                id = parametr.pop();
                lastStatus = parametr.pop();

                if(lastStatus == '0'){                    
                    $('#status'+id).html('تایید شده');
                    parametr.push('1');
                    parametr.push(id);
                    newHref = parametr.join();
                    $(this).parent().attr('href',newHref);                       
                }
                else if(lastStatus == '1'){                      
                    $('#status'+id).html('تایید نشده');
                    parametr.push('0');
                    parametr.push(id);
                    newHref = parametr.join();
                    $(this).parent().attr('href',newHref);                    
                }                     
        });
   });
});