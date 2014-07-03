/*keep tabs persistent */
$(document).ready(function() 
{ 

	var sectTitle = $( "section.title h4" ).html();

	if(sessionStorage['tab_id'] !== '' &&  sectTitle !== sessionStorage['section'])
	{
		sessionStorage['tab_id'] = 0;	
	}

	sessionStorage['section'] = sectTitle;

	$('.tabs').tabs({
		load:function(event,ui){
		}
	});
	
	$('.tab-menu a').each(function(){
		$(this).click(function(){
			index = $('.tabs a[href="' + $(this).attr('href') + '"]').parent().index();
			sessionStorage['tab_id']= index;
		});
	});

	
	$('.tabs').tabs('select', parseInt(sessionStorage['tab_id']));

}); // end doc ready 

