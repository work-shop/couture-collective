<?php

class acf_field_gallery extends acf_field
{

	var $settings;
	
	
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'gallery';
		$this->label = __("Gallery",'acf');
		$this->category = __("Content",'acf');
		$this->defaults = array(
			'preview_size'	=>	'thumbnail',
			'library'		=>	'all'
		);
		$this->l10n = array(
			'select'		=>	__("Add Image to Gallery",'acf'),
			'edit'			=>	__("Edit Image",'acf'),
			'update'		=>	__("Update Image",'acf'),
			'uploadedTo'	=>	__("uploaded to this post",'acf'),
			'count_0'		=>	__("No images selected",'acf'),
			'count_1'		=>	__("1 image selected",'acf'),
			'count_2'		=>	__("%d images selected",'acf'),
		);
		
		
		// lang
		load_textdomain('acf', dirname(__FILE__) . '/lang/acf-gallery-' . get_locale() . '.mo');
		
		
		// do not delete!
    	parent::__construct();
    	
    	
    	// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.1.1'
		);
	}
	
	
	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add css + javascript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// register acf scripts
		wp_register_script( 'acf-input-gallery', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version'] );
		wp_register_style( 'acf-input-gallery', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] ); 
		
		
		// scripts
		wp_enqueue_script(array(
			'acf-input-gallery',	
		));

		// styles
		wp_enqueue_style(array(
			'acf-input-gallery',	
		));
		
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		?>
<div class="acf-gallery" data-preview_size="<?php echo $field['preview_size']; ?>" data-library="<?php echo $field['library']; ?>">
	
	<input type="hidden" name="<?php echo $field['name']; ?>" value="" />
	
	<div class="thumbnails">
		<div class="inner clearfix">
		<?php if( $field['value'] ): foreach( $field['value'] as $attachment ): 
			
			$src = '';
			
			if( strpos($attachment['mime_type'], 'image') !== false )
			{
				$src = wp_get_attachment_image_src( $attachment['id'], $field['preview_size'] );
				$src = $src[0];
			}
			else
			{
				$src = wp_mime_type_icon( $attachment['id'] );
			}

			?>
			<div class="thumbnail" data-id="<?php echo $attachment['id']; ?>">
				<input class="acf-image-value" type="hidden" name="<?php echo $field['name']; ?>[]" value="<?php echo $attachment['id']; ?>" />
				<div class="inner clearfix">
					<img src="<?php echo $src; ?>" alt="" />
					<div class="list-data">
						<table>
							<tbody>
							<tr>
								<th><label><?php _e("Title",'acf'); ?>:</label></th>
								<td class="td-title"><?php echo $attachment['title']; ?></td>
							</tr>
							<tr>
								<th><label><?php _e("Alternate Text",'acf'); ?>:</label></th>
								<td class="td-alt"><?php echo $attachment['alt']; ?></td>
							</tr>
							<tr>
								<th><label><?php _e("Caption",'acf'); ?>:</label></th>
								<td class="td-caption"><?php echo $attachment['caption']; ?></td>
							</tr>
							<tr>
								<th><label><?php _e("Description",'acf'); ?>:</label></th>
								<td class="td-description"><?php echo $attachment['description']; ?></td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
				<div class="hover">
					<ul class="bl">
						<li><a href="#" class="acf-button-delete ir"><?php _e("Remove",'acf'); ?></a></li>
						<li><a href="#" class="acf-button-edit ir"><?php _e("Edit",'acf'); ?></a></li>
					</ul>
				</div>
				
			</div>
		<?php endforeach; endif; ?>
		</div>
	</div>

	<div class="toolbar">
		<ul class="hl clearfix">
			<li class="add-image-li"><a class="acf-button add-image" href="#"><?php _e("Add Image",'acf'); ?></a></li>
			<li class="gallery-li view-grid-li active"><div class="divider divider-left"></div><a class="ir view-grid" href="#"><?php _e("Grid",'acf'); ?></a><div class="divider"></div></li>
			<li class="gallery-li view-list-li"><a class="ir view-list" href="#"><?php _e("List",'acf'); ?></a><div class="divider"></div></li>
			<li class="gallery-li count-li right">
				<span class="count"></span>
			</li>
		</ul>
	</div>
	
	<script type="text/html" class="tmpl-thumbnail">
	<div class="thumbnail" data-id="{id}">
		<input type="hidden" class="acf-image-value" name="<?php echo $field['name']; ?>[]" value="{id}" />
		<div class="inner clearfix">
			<img src="{url}" alt="{alt}" />
			<div class="list-data">
				<table>
				<tbody>
					<tr>
						<th><label><?php _e("Title",'acf'); ?>:</label></th>
						<td class="td-title">{title}</td>
					</tr>
					<tr>
						<th><label><?php _e("Alternate Text",'acf'); ?>:</label></th>
						<td class="td-alt">{alt}</td>
					</tr>
					<tr>
						<th><label><?php _e("Caption",'acf'); ?>:</label></th>
						<td class="td-caption">{caption}</td>
					</tr>
					<tr>
						<th><label><?php _e("Description",'acf'); ?>:</label></th>
						<td class="td-description">{description}</td>
					</tr>
				</tbody>
			</table>
			</div>
		</div>
		<div class="hover">
			<ul class="bl">
				<li><a href="#" class="acf-button-delete ir"><?php _e("Remove",'acf'); ?></a></li>
				<li><a href="#" class="acf-button-edit ir"><?php _e("Edit",'acf'); ?></a></li>
			</ul>
		</div>
		
	</div>
	</script>
	
</div>
		<?php
	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		// vars
		$key = $field['name'];
		
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Preview Size",'acf'); ?></label>
		<p><?php _e("Shown when entering data",'acf') ?></p>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][preview_size]',
			'value'		=>	$field['preview_size'],
			'layout'	=>	'horizontal',
			'choices' 	=>	apply_filters('acf/get_image_sizes', array())
		));

		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Library",'acf'); ?></label>
		<p><?php _e("Limit the media library choice",'acf') ?></p>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][library]',
			'value'		=>	$field['library'],
			'layout'	=>	'horizontal',
			'choices' 	=>	array(
				'all' => __('All', 'acf'),
				'uploadedTo' => __('Uploaded to post', 'acf')
			)
		));

		?>
	</td>
</tr>
		<?php
		
	}
	
	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value( $value, $post_id, $field )
	{
		$new_value = array();
		
		
		// empty?
		if( empty($value) || !is_array($value) )
		{
			return $value;
		}
		
		
		// find attachments (DISTINCT POSTS)
		$attachments = get_posts(array(
			'post_type' => 'attachment',
			'numberposts' => -1,
			'post_status' => null,
			'post__in' => $value,
		));
		
		$ordered_attachments = array();
		foreach( $attachments as $attachment)
		{
			// create array to hold value data
			$ordered_attachments[ $attachment->ID ] = array(
				'id'			=>	$attachment->ID,
				'alt'			=>	get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title'			=>	$attachment->post_title,
				'caption'		=>	$attachment->post_excerpt,
				'description'	=>	$attachment->post_content,
				'mime_type'		=>	$attachment->post_mime_type,
			);
			
		}
		
		
		// override value array with attachments
		foreach( $value as $v)
		{
			if( isset($ordered_attachments[ $v ]) )
			{
				$new_value[] = $ordered_attachments[ $v ];
			}
		}
		
		
		// return value
		return $new_value;	
	}
	
	
	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		$value = $this->format_value( $value, $post_id, $field );
		
		// find all image sizes
		$image_sizes = get_intermediate_image_sizes();


		if( $value )
		{
			foreach( $value as $k => $v )
			{
				if( strpos($v['mime_type'], 'image') !== false )
				{
					// is image
					$src = wp_get_attachment_image_src( $v['id'], 'full' );
					
					$value[ $k ]['url'] = $src[0];
					$value[ $k ]['width'] = $src[1];
					$value[ $k ]['height'] = $src[2];
					
					
					// sizes
					if( $image_sizes )
					{
						$value[$k]['sizes'] = array();
						
						foreach( $image_sizes as $image_size )
						{
							// find src
							$src = wp_get_attachment_image_src( $v['id'], $image_size );
							
							// add src
							$value[ $k ]['sizes'][ $image_size ] = $src[0];
							$value[ $k ]['sizes'][ $image_size . '-width' ] = $src[1];
							$value[ $k ]['sizes'][ $image_size . '-height' ] = $src[2];
						}
						// foreach( $image_sizes as $image_size )
					}
					// if( $image_sizes )
				}
				else
				{
					// is file
					$src = wp_get_attachment_url( $v['id'] );
					
					$value[ $k ]['url'] = $src;
				}	
			}
			// foreach( $value as $k => $v )
		}
		// if( $value )
		
		
		// return value
		return $value;
	}
   	
}

new acf_field_gallery();

?>
