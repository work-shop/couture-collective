
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


});//end document.ready


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
		var trim = $(window).height();		
		$('html,body').css('height',trim);
	}
	
	else if($('#header').hasClass('on')){
		$('#header').removeClass('on');
		$('#header').addClass('off');
		$('html').removeClass('header-open');
		$('html').addClass('header-closed');
		$('#header').scrollTop(0);	
		$('html').css('height','100%');
		$('body').css('height','100%');						
	}
	
}


//measure, resize, and adjust the viewport
function view(){
	
	ch = $(window).height();
	cw = $(window).width();
	hh = $("#header").height();
	console.log(hh);
	
	if($(window).width() >= 768){	
		$('.block.half').css('height',ch/2);				
		$('.block.crop').css('height',ch);		
		$('.block.min').css('min-height',ch);						
		$('.block.fit').css('height',ch-hh);				
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




