(function($){
	
	/*
	*  Gallery
	*
	*  static model for this field
	*
	*  @type	event
	*  @date	28/07/13
	*
	*/
	
	
	// reference
	var _media = acf.media;
	
	
	// field
	acf.fields.gallery = {
		
		$el : null,
		
		o : {},
		
		set : function( o ){
			
			// merge in new option
			$.extend( this, o );
			
			
			// find input
			this.$input = this.$el.children('input[type="hidden"]');
			
			
			// get options
			this.o = acf.helpers.get_atts( this.$el );
			
			
			// wp library query
			this.o.query = {};
			
			
			// library
			if( this.o.library == 'uploadedTo' )
			{
				this.o.query.uploadedTo = acf.o.post_id;
			}
			
			
			// view
			this.o.view = 'grid';
			if( this.$el.hasClass('view-list') )
			{
				this.o.view = 'list';
			}
			
			
			// return this for chaining
			return this;
			
		},
		init : function(){

			// is clone field?
			if( acf.helpers.is_clone_field(this.$input) )
			{
				return;
			}
			
			
			// update count
			this.render();
			
			
			// sortable
			this.$el.find('> .thumbnails > .inner').sortable({
				items					:	'> .thumbnail',
				forceHelperSize			:	true,
				forcePlaceholderSize	:	true,
				scroll					:	true,
				start					:	function (event, ui) {
				
					// alter width / height to allow for 2px border
					ui.placeholder.width( ui.placeholder.width() - 4 );
					ui.placeholder.height( ui.placeholder.height() - 4 );
					
	   			}
			});
					
		},
		view : function( view ){
			
			this.o.view = view;
			
			this.render();
			
		},
		render : function(){
			
			// vars
			var count	=	this.$el.find('.thumbnails .thumbnail').length,
				s		=	acf.l10n.gallery[ 'count_' + Math.min( count, 2) ].replace('%d', count),
				$span	=	this.$el.find('.toolbar .count');
			
			
			// update span text
			$span.html( s );
			
			
			// toolbar
			if( this.o.view == 'list' )
			{
				this.$el.addClass('view-list');
				this.$el.find('.toolbar .view-grid-li').removeClass('active');
				this.$el.find('.toolbar .view-list-li').addClass('active');
			}
			else
			{
				this.$el.removeClass('view-list');
				this.$el.find('.toolbar .view-grid-li').addClass('active');
				this.$el.find('.toolbar .view-list-li').removeClass('active');
			}
			
			
			
		},
		add : function( image ){
			
			// IMPORTANT: this may not be the field we think it is. 
			// Opening a popup will cause the acf/setup_fields action to run, and this may be updated with the fields found in the popup! 
			// Reset this using the _media.div
			
			
			// reset
			this.set({ $el : _media.div });
			
			
			// vars
			var tmpl = this.$el.find('.tmpl-thumbnail').html();
			
			
			// update tmpl
			$.each(image, function( k, v ){
				var regex = new RegExp('{' + k + '}', 'g');
				tmpl = tmpl.replace(regex, v);
			});
		
		
			// add div
		    this.$el.find('.thumbnails > .inner').append( tmpl );
			
			
			// update gallery count
			this.render();
			
				 	
		 	// validation
			this.$el.closest('.field').removeClass('error');
	
		},
		edit : function( id ){
			
			// set global var
			_media.div = this.$el;
			

			// clear the frame
			_media.clear_frame();
			
			
			// create the media frame
			_media.frame = wp.media({
				title		:	acf.l10n.gallery.edit,
				multiple	:	false,
				button		:	{ text : acf.l10n.gallery.update }
			});
			
			
			// open
			_media.frame.on('open',function() {
				
				// set to browse
				if( _media.frame.content._mode != 'browse' )
				{
					_media.frame.content.mode('browse');
				}
				
				
				// add class
				_media.frame.$el.closest('.media-modal').addClass('acf-media-modal acf-expanded');
			
				// set selection
				var selection	=	_media.frame.state().get('selection'),
					attachment	=	wp.media.attachment( id );
				
				
				attachment.fetch();
				selection.add( attachment );
				
				
				// change events for list view
				_media.frame.$el.on('change', '.setting input, .setting textarea', function(){
					
					// vars
					var $el = $(this),
						setting = $el.closest('.setting').attr('data-setting');
					
					
					// update galleries
					$('.acf-gallery .thumbnail[data-id="' + id + '"] .td-' + setting).html( $el.val() );
					
				});
							
			});
			
			
			// close
			_media.frame.on('close',function(){
			
				// remove class
				_media.frame.$el.closest('.media-modal').removeClass('acf-media-modal');
				
			});
			
							
			// Finally, open the modal
			_media.frame.open();
			
		},
		remove : function( id ){
			
			// reference
			var _this = this;
			
			
			// vars
			$thumb = this.$el.find('.thumbnails .thumbnail[data-id="' + id + '"]');
			
			
			// fade and remove
			$thumb.animate({ opacity : 0 }, 250, function(){
				
				$thumb.remove();
				
				_this.render();
				
			});
			
		},
		render_collection : function(){
			
			// Note: Need to find a differen 'on' event. Now that attachments load custom fields, this function can't rely on a timeout. Instead, hook into a render function foreach item
			
			// set timeout for 0, then it will always run last after the add event
			setTimeout(function(){
			
			
			// vars
			var $gallery	=	_media.div,
				$content	=	_media.frame.content.get().$el

				collection	=	_media.frame.content.get().collection || null;
				
			
			if( collection )
			{
				var i = -1;
				
				collection.each(function( item ){
					
					i++;
					
					var $li = $content.find('.attachments > .attachment:eq(' + i + ')');
					
					
					// if image is already inside the gallery, disable it!
					if( $gallery.find('.thumbnails .thumbnail[data-id="' + item.id + '"]').exists() )
					{
						item.off('selection:single');
						$li.addClass('acf-selected');
					}
					
				});
			}
			
			
			}, 0);
				
		},
		popup : function()
		{
			// reference
			var _this = this;
			
			
			// set global var
			_media.div = this.$el;
			

			// clear the frame
			_media.clear_frame();
			
			
			 // Create the media frame
			 _media.frame = wp.media({
				states : [
					new wp.media.controller.Library({
						library		:	wp.media.query( this.o.query ),
						multiple	:	true,
						title		:	acf.l10n.gallery.select,
						priority	:	20,
						filterable	:	'all'
					})
				]
			});
			
			
			// customize model / view
			_media.frame.on('content:activate', function(){
				
				// vars
				var toolbar = null,
					filters = null;
					
				
				// populate above vars making sure to allow for failure
				try
				{
					toolbar = _media.frame.content.get().toolbar;
					filters = toolbar.get('filters');
				} 
				catch(e)
				{
					// one of the objects was 'undefined'... perhaps the frame open is Upload Files
					//console.log( e );
				}
				
				
				// validate
				if( !filters )
				{
					return false;
				}
				
				
				// no need for 'uploaded' filter
				if( _this.o.library == 'uploadedTo' )
				{
					filters.$el.find('option[value="uploaded"]').remove();
					filters.$el.after('<span>' + acf.l10n.gallery.uploadedTo + '</span>')
					
					$.each( filters.filters, function( k, v ){
						
						v.props.uploadedTo = acf.o.post_id;
						
					});
				}
				
				
				// hide selected items from the library
				_this.render_collection();
				 
				_media.frame.content.get().collection.on( 'reset add', function(){
				    
					_this.render_collection();
				    
			    });
			    
								
			});
			
			
			// When an image is selected, run a callback.
			_media.frame.on( 'select', function() {
				
				// get selected images
				selection = _media.frame.state().get('selection');
				
				if( selection )
				{
					selection.each(function(attachment){
	
						
						// is image already in gallery?
						if( _this.$el.find('.thumbnails .thumbnail[data-id="' + attachment.id + '"]').exists() )
						{
							return;
						}
											
						
				    	// vars
				    	var image = {
					    	id			:	attachment.id,
					    	title		:	attachment.attributes.title,
					    	name		:	attachment.attributes.filename,
					    	url			:	attachment.attributes.url,
					    	caption 	:	attachment.attributes.caption,
						    alt			:	attachment.attributes.alt,
						    description	:	attachment.attributes.description
				    	};
				    	
				    	
				    	// file?
					    if( attachment.attributes.type != 'image' )
					    {
						    image.url = attachment.attributes.icon;
					    }
					    
					    
					    // is preview size available?
				    	if( attachment.attributes.sizes && attachment.attributes.sizes[ _this.o.preview_size ] )
				    	{
					    	image.url = attachment.attributes.sizes[ _this.o.preview_size ].url;
				    	}
					    
					    
				    	// add file to field
				        _this.add( image );
				        
						
				    });
				    // selection.each(function(attachment){
				}
				// if( selection )
				
				
			});
			// _media.frame.on( 'select', function() {
					 
			
			// Finally, open the modal
			_media.frame.open();
				
			
			return false;
		}
		
	};
	
	
	/*
	*  Events
	*
	*  jQuery events for this field
	*
	*  @type	function
	*  @date	1/03/2011
	*
	*  @param	N/A
	*  @return	N/A
	*/
	
	$(document).on('click', '.acf-gallery .acf-button-edit', function( e ){
		
		e.preventDefault();
		
		// vars
		var id = $(this).closest('.thumbnail').attr('data-id');
		
		acf.fields.gallery.set({ $el : $(this).closest('.acf-gallery') }).edit( id );
		
		$(this).blur();
			
	});
	
	$(document).on('click', '.acf-gallery .acf-button-delete', function( e ){
		
		e.preventDefault();
		
		// vars
		var id = $(this).closest('.thumbnail').attr('data-id');
		
		acf.fields.gallery.set({ $el : $(this).closest('.acf-gallery') }).remove( id );
		
		$(this).blur();
			
	});
	
	$(document).on('click', '.acf-gallery .add-image', function( e ){
		
		e.preventDefault();
		
		acf.fields.gallery.set({ $el : $(this).closest('.acf-gallery') }).popup();
		
		$(this).blur();
		
	});
	
	$(document).on('click', '.acf-gallery .view-grid', function( e ){
		
		e.preventDefault();
		
		acf.fields.gallery.set({ $el : $(this).closest('.acf-gallery') }).view( 'grid' );
		
		$(this).blur();
		
	});
	
	$(document).on('click', '.acf-gallery .view-list', function( e ){
		
		e.preventDefault();
		
		acf.fields.gallery.set({ $el : $(this).closest('.acf-gallery') }).view( 'list' );
		
		$(this).blur();
		
	});
	
	
	/*
	*  acf/setup_fields
	*
	*  run init function on all elements for this field
	*
	*  @type	event
	*  @date	20/07/13
	*
	*  @param	{object}	e		event object
	*  @param	{object}	el		DOM object which may contain new ACF elements
	*  @return	N/A
	*/
	
	$(document).on('acf/setup_fields', function(e, el){
		
		$(el).find('.acf-gallery').each(function(){
			
			acf.fields.gallery.set({ $el : $(this) }).init();
			
		});
		
	});
	
	

})(jQuery);
