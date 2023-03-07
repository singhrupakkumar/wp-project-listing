/*
 * Citadela Theme mobile javascripts
 *
 */

"use strict";

function citadela_isResponsive(width){
	var w=window,
		d=document,
		e=d.documentElement,
		g=d.getElementsByTagName('body')[0],
		x=w.innerWidth||e.clientWidth||g.clientWidth;
	return x < parseInt(width);
}

function citadela_isUserAgent(type){
	return navigator.userAgent.toLowerCase().indexOf(type.toLowerCase()) > -1;
}

function citadela_isMobile(){
	// maybe inherit modernizr.touchevents
	//return isResponsive(640) && (isUserAgent('android') || isUserAgent('iphone') || isUserAgent('ipad') || isUserAgent('ipod'));
	return isUserAgent('mobile');
}

function citadela_isTablet(){
	// maybe inherit modernizr.touchevents
	return citadela_isResponsive(1024) && ! citadela_isMobile();
}

function citadela_isDesktop(){
	return ! citadela_isMobile() && ! citadela_isTablet();
}

function citadela_isAndroid(){
	return isUserAgent('android');
}

function citadela_isIpad(){
	return isUserAgent('ipad');
}

function citadela_isTouch(){
	return (('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0));
}

function citadela_emToPx(input) {
	var input = parseFloat(input);
    var emSize = parseFloat(jQuery("body").css("font-size"));
    return (emSize * input);
}

function citadela_pxToEm(input) {
	var input = parseInt(input);
    var emSize = parseFloat(jQuery("body").css("font-size"));
    return (input / emSize);
}