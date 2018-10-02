$(document).ready(function(){
	//Caption Sliding (Partially Hidden to Visible)
	$('.boxgrid').hover(function(){
		$(".boxcaption", this).stop().animate({top:'170px'},{queue:false,duration:180});
	}, function() {
		$(".boxcaption", this).stop().animate({top:'230px'},{queue:false,duration:180});
	});
});