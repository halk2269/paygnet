$(document).ready(function(){
	$('.sidemenu li > a').click(function(e){
		if (!$(this).parent().hasClass('open')) {
			$(this).parents('.sidemenu').children('li.open').find('ul').slideUp(500, function(){
				$(this).parent().removeClass('open');
			});
			$(this).parent().find('ul').slideDown(500, function() {$(this).parent().addClass('open')});
		}
		e.preventDefault();
	})
});