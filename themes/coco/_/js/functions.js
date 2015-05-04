
//global variables
var cw,ch,hh;
var loaded = false;
var state = 'intro';
var moving = false;

//initial events, and general event binding
jQuery(document).ready(function($) {

	view();
	
	$('#backtotop').click(function(event) {
	  	event.preventDefault();
		$('body,html').animate({scrollTop:0},2000);
	});
	
	$('.menu-toggle').click(function(event) {
	  	event.preventDefault();
		menuToggle();
	});

	$(".jump").click(function(e){
		e.preventDefault();
		var href = $(this).attr("href");
		href = href.toLowerCase();
		scrollLink(href);	
	});

	$('#make-pre-reservation').on('submit', function() {
		$('#pre-reservation-add-to-cart').prop('disabled', true);
	});
	
	$('#look-book-modal').modal('show');	
	
	$('#main-image').click(function(event) {
	  	event.preventDefault();
		//imageToggle();
	});	



	(function() {
		var native_width = native_height = 0;

		$('.magnifier').css({
			'background': 'url(' + $('.magnifier').attr('src-giant') + ') no-repeat'
		});

		$('.small-image').on('click', function( e )  {
			var 	dur 	= 300,
			 	sm 	= $(this).find('img'),
			 	lg 	= $('#main-image').find('img');

			var 	sm_src_lg = sm.attr('src-large'),
			    	sm_src_sm = sm.attr('src'),
			    	sm_src_gi = sm.attr('src-giant');

				lg_src_lg = lg.attr('src'),
				lg_src_sm = lg.attr('src-small'),
				lg_src_gi = lg.attr('src-giant');

			sm.animate({'opacity': 0}, dur, function() {
				sm.attr('src', lg_src_sm);
				sm.attr('src-large', lg_src_lg);
				sm.attr('src-giant', lg_src_gi);
				sm.animate({'opacity': 1});
			});

			lg.animate({'opacity': 0}, dur, function() {
				lg.attr('src', sm_src_lg);
				lg.attr('src-small', sm_src_sm);	
				lg.attr('src-giant', sm_src_gi);

				$('.magnifier').css({
					'background': 'url(' + sm_src_gi + ') no-repeat'
				})
				$('.magnifier').attr('src-giant', sm_src_gi );

				native_width = native_height = 0;

				lg.animate({'opacity': 1});
			});

		});

		$('#main-image').mousemove(function(e) {
			if ( !(native_width || native_width) ) {
				var im = new Image();
				im.src = $(this).find('img').attr('src');
				native_width = im.width;
				native_height = im.height;
			} else {
				var m_offset = $(this).offset();
				
				var mx = e.pageX - m_offset.left,
				    my = e.pageY - m_offset.top;


				if ( mx < $(this).width() && my < $(this).height() && mx > 0 && my > 0 ) {
					$('.magnifier').fadeIn(100);
				} else {
					$('.magnifier').fadeOut(100);
				}

				if ( $('.magnifier').is(':visible') ) {
					var rx = Math.round(mx / $(this).find('img').width() * native_width - $('.magnifier').width()/2) * -1;
					var ry = Math.round(my / $(this).find('img').height() * native_height - $('.magnifier').height()/2) * -1;

					var bgp = rx + 'px ' + ry + 'px';

					var px = mx - $('.magnifier').width() / 2;
					var py = my - $('.magnifier').height() / 2; 

					$('.magnifier').css({ left:px, top: py, backgroundPosition: bgp });
				}

			}
		});

	
	})();

	$('')
	
	$('#tabs-nav li').click(function(e){
		e.preventDefault();
	    if (!$(this).hasClass('active')) {
	        var tabNum = $(this).index();
	        var nthChild = tabNum+1;
	        $('#tabs-nav li.active').removeClass('active');
	        $(this).addClass('active');
	        $('#tab li.active').removeClass('active');
	        $('#tab li:nth-child('+nthChild+')').addClass('active');
	    }
	});
	  

});//end document.ready

$(window).ready(function() {

	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();


});//end window.ready


$(window).resize(function() {

	view();	
	
});//end window.resize


$(window).scroll(function() { 

	if($('#state').hasClass('spy')){
		spy();
	}	

	var after = $('body').offset().top + 60;
	       
	if($(this).scrollTop() >= after && $("body").hasClass('before')){
		$("body").removeClass('before').addClass('after');
	} 
	else if($(this).scrollTop() < after && $("body").hasClass('after')){
		$("body").removeClass('after').addClass('before');	
	} 

});//end window.scroll


//FUNCTIONS

//m or M	
$(document).keypress(function(e) {
	if(e.which == 109 || e.which == 77) {
		navToggle();
	}	
});

//initialize flexslider slideshows
function flexsliderSetup(){

	$('.flexslider').flexslider({
	      animation: 'slide',
	      controlsContainer: '.flexslider-controls',
	      slideshowSpeed: 5000,           
		  animationSpeed: 250,
	      directionNav: true
	 });			 
	 	 	
}

//animate jump links
function scrollLink(destination){
	$('html,body').animate({
		scrollTop: $(destination).offset().top - 0
	},1000);
}

//open and close the menu
function menuToggle(){
	
	if($('#header').hasClass('off')){
		$('#header').removeClass('off');
		$('#header').addClass('on');
		$('html').removeClass('header-closed');
		$('html').addClass('header-open');	
	}
	
	else if($('#header').hasClass('on')){
		$('#header').removeClass('on');
		$('#header').addClass('off');
		$('html').removeClass('header-open');
		$('html').addClass('header-closed');				
	}
	
}

function imageToggle(){

	if($('#main-image').hasClass('off')){
		$('#main-image').removeClass('off');
		$('#main-image').addClass('on');
		$('html').removeClass('image-closed');
		$('html').addClass('image-open');	
			
	}
	
	else if($('#main-image').hasClass('on')){
		$('#main-image').removeClass('on');
		$('#main-image').addClass('off');
		$('html').removeClass('image-open');
		$('html').addClass('image-closed');			
	}
	
}



//measure, resize, and adjust the viewport
function view(){
	
	ch = $(window).height();
	cw = $(window).width();
	hh = $("#header").height();
	
	if($(window).width() >= 768){	
		$('.block.half').css('height',ch/2);				
		$('.block.crop').css('height',ch);		
		$('.block.min').css('min-height',ch);						
		$('.block.fit').css('height',ch-90);				
	}
	else{
		$('.block.crop').css('min-height',ch);	
		$('.block.min').css('min-height',ch);							
	}
	
	if(!loaded){
		loadPage();
	}		

}

//once all elements are sized, slideshows initialized, fade in the content
function loadPage(){
	loaded = true;
	
	flexsliderSetup();

	setTimeout(function(){
		$('.loading').addClass('loaded');
		$('.landing').addClass('landed');
	},500);		
		
}

//determine state of the users view on the page by scroll position 
function spy(){

	var targets = new Array();
	
	$('#nav-side .jump').each(function(i){
		targets[i] = new Array(3);
		var temp = $(this).attr('href');
		var offset = $(temp).offset();	
		targets[i][0] = $(this);		
		targets[i][1] = offset.top;
		targets[i][2] = $(temp);
		
	});
	
	for(var j = 0; j < targets.length; j++){
		if(($(window).scrollTop()+180) >= targets[j][1]){
			$('.block').removeClass('active');					
			$('#nav-side .jump').removeClass('active');		
			targets[j][0].addClass('active');		
			targets[j][2].addClass('active');			
				
		}
	}	
	
}

// nic's additions

/*
 *
 *
 */
 // jQuery(document).ready(function($) {
 // 	var a = $('#all'), b = $('#tomorrow');

 // 	a.on('click', function( e ) {
 // 		if ( a.hasClass('active') ) return;


 // 	});

 // 	b.on('click', function( e ) {
 // 		if ( b.hasClass('active') ) return;

 // 	});

 // 	function cycleTo( selector ) {
 // 		return function(e) {
 // 			var c = $(this);
 // 			if ( !c.hasClass('active') ) {
 // 				$('.filter-option').removeClass('active');
 // 				c.addClass('active');

 // 				$( '.shared-dress' ).filter( selector ).
 // 			}



 // 		}
 // 	}

 // });





