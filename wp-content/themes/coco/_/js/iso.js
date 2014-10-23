$(function(){
      
    var $container = $('#container');	  
	var $optionSets = $('#isotope-filters'),
	$optionLinks = $optionSets.find('a.filter');    
      
	$container.isotope({
	  masonry: {
	    		columnWidth: 160
	    		}
	});
			
	$.Isotope.prototype._masonryResizeChanged = function() {
		 return true;
	};

  $.Isotope.prototype._masonryReset = function() {
    // layout-specific props
    this.masonry = {};
    this._getSegments();
    var i = this.masonry.cols;
    this.masonry.colYs = [];
    while (i--) {
      this.masonry.colYs.push( 0 );
    }
    
    };
       
	
	$optionLinks.click(function(e){
		e.preventDefault();
		var $this = $(this);
		

		
		if ( $this.hasClass('hierarchical') ) {
			var targetMenu = $this.data('sub-target');
			$('.sub-menu').addClass('hidden');
			$(targetMenu).removeClass('hidden');
			//console.log($kids);

		}

		
		if ( $this.hasClass('selected') ) {
		  return false;
		}

		
		$optionLinks.removeClass('selected');
		$this.addClass('selected');
		
		// make option object dynamically, i.e. { filter: '.my-filter-class' }
		var options = {},
		    key = $optionSets.attr('data-option-key'),
		    value = $this.attr('data-option-value');
		// parse 'false' as false boolean
		value = value === 'false' ? false : value;
		options[ key ] = value;
		
		if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {
		  // changes in layout modes need extra logic
		  changeLayoutMode( $this, options )  
		} else {
		  // otherwise, apply new options
		  	$container.isotope( options
		  	, function(){
				pos = $container.offset();
			
			 $('body,html').animate({
			     scrollTop: pos.top - 100
			}, 700);			  	
		  	}
		  	
		  	 );	
		  
		}
		
		return false;
		
		});
		
});