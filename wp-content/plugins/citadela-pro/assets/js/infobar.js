jQuery(document).ready(function(){
	var $infobar = jQuery('#citadela-infobar');
	var cname = 'citadela_infobar';
	var currentExpiration = jQuery('#citadela-infobar').data('cexp');
	var cookieDefaults = { on: 1, expires: currentExpiration };
	var cookie = Cookies.get(cname);
	if(typeof cookie !== 'undefined'){
		//cookie is saved
		var c = JSON.parse(cookie);
		//check saved and current expiration time
		var savedExpiration = c.expires;
		if(savedExpiration != currentExpiration ){
			//expiration time was changed in admin settings, remove cookie and show infobar
			Cookies.remove(cname);
			$infobar.show();
			jQuery('#citadela-infobar .button').on('click', function(e){
				Cookies.set(cname, JSON.stringify(cookieDefaults), { expires: currentExpiration });
				$infobar.fadeOut();
			});
		}
	}else{
		//cookie is not saved in browser, show infobar
		$infobar.show();
		jQuery('#citadela-infobar .button').on('click', function(e){
			Cookies.set(cname, JSON.stringify(cookieDefaults), { expires: currentExpiration });
			$infobar.fadeOut();
		});
	}
});
