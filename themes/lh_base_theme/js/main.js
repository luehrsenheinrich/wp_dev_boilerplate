// @depend "plugins.js"

//get dat fonts!
/*WebFontConfig = {
	google: { families: [ 'Open+Sans:400,600,400italic,600italic:latin' ] }
};

(function() {
	var wf = document.createElement('script');
	wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
	wf.type = 'text/javascript';
	wf.async = 'true';
	var s = document.getElementsByTagName('script')[0];
	s.parentNode.insertBefore(wf, s);
})();/**/

//jQuery
var $ = jQuery;
$(document).ready(function(){
	// Mobile Menu
	$(".mobile-nav-trigger").click(function(){
		$("body").toggleClass("nav_open");
	});

	
	$(".phone-menu-lightbox").click(function(){
		$("body").removeClass("nav_open");
	});
});