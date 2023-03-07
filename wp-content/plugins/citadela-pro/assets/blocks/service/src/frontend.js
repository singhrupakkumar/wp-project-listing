jQuery(document).ready(function(){
	"use strict";
	checkServiceWidth();
});

jQuery(window).resize(function(){
	checkServiceWidth();
});

function checkServiceWidth(){
	const widthBreakpoint = 400;
	jQuery('.citadela-block-service').each(function(){
		const $serviceDiv = jQuery(this);
		const width = $serviceDiv.width();
		if(width >= widthBreakpoint ){
			$serviceDiv.removeClass('narrow').addClass('standard');
		}else{
			$serviceDiv.removeClass('standard').addClass('narrow');
		}
	});
}