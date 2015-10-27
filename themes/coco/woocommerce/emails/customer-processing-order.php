<?php
/**
 * Customer processing order email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<?php foreach ($order->get_items() as $key => $value) {
		$product = wc_get_product( ws_fst( $value['item_meta']['_product_id'] ));
		$product_type = ( is_array($ts = get_the_terms( $product->id, 'product_cat')) ) ? ws_fst( $ts )->name : ""; ?>


	<?php if ( $product->product_type == "booking" ) { ?>

		<?php $dress_id = CC_Controller::get_dress_for_product( $product->id, "rental" ); ?>

		<?php if ( strtotime( $value['Booking Date'] ) > strtotime( '+1 day' )) {  // This is an advance reservation ?>


				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
				    <tbody class="mcnTextBlockOuter">
				        <tr>
				            <td valign="top" class="mcnTextBlockInner">
				                
				                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
				                    <tbody><tr>
				                        
				                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
				                        
				                            <p class="null" style="text-align: center;line-height:2.4;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">Your reservation of<br>
				<span style="color: #B98076;font-size: 18px;text-transform: uppercase;"><?php echo get_field('dress_designer', $dress_id); ?><br>
				<?php echo get_field('dress_description', $dress_id); ?></span><br>
				will be delivered on <strong><?php echo date( "F d Y", strtotime( $value['Booking Date'] . " -1 days" ) ); ?></strong> at&nbsp;5pm.</span></span></span></p>

				                        </td>
				                    </tr>
				                </tbody></table>
				                
				            </td>
				        </tr>
				    </tbody>
				</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
				    <tbody class="mcnDividerBlockOuter">
				        <tr>
				            <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 18px 18px 48px;">
				                <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="70%" style="border-top-width: 1px;border-top-style: solid;border-top-color: #4A443E; margin:0 auto;">
				                    <tbody><tr>
				                        <td>
				                            <span></span>
				                        </td>
				                    </tr>
				                </tbody></table>
				<!--            
				                <td class="mcnDividerBlockInner" style="padding: 18px;">
				                <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
				-->
				            </td>
				        </tr>
				    </tbody>
				</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
				    <tbody class="mcnTextBlockOuter">
				        <tr>
				            <td valign="top" class="mcnTextBlockInner">
				                
				                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
				                    <tbody><tr>
				                        
				                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
				                        
				                            <p class="null" style="text-align: center;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">Please have it ready to pick up on the morning of <strong><?php echo date( "F d Y", strtotime( $value['Booking Date'] . " +1 days" ) ); ?></strong>.</span></span></span></p>

				                        </td>
				                    </tr>
				                </tbody></table>
				                
				            </td>
				        </tr>
				    </tbody>
				</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
				    <tbody class="mcnTextBlockOuter">
				        <tr>
				            <td valign="top" class="mcnTextBlockInner">
				                
				                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
				                    <tbody><tr>
				                        
				                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 30px; padding-bottom: 9px; padding-left: 30px;">
				                        
				                            <p class="null" style="text-align: center;"><span style="color:#4A443E"><span style="font-family:georgia,times,times new roman,serif">If you need to extend this reservation for any reason, please email your request to <a href="mailto:info@couturecollective.club" target="_blank">info@couturecollective.club</a>. Extensions are dependent on availability. A one-week reservation can sometimes be arranged by using two of your pre-reservation dates. Please also be aware that, out of respect for the sartorial rights of all our members, failure to have an item ready for pick up on the designated date will result in a $100/day automatic late fee. Cancellations may be made on the website up to one week prior to the reservation. If you need to cancel a reservation within the week, please email your cancellation request to <a href="mailto:info@couturecollective.club" target="_blank">info@couturecollective.club</a>. Cancellations cannot be made within 48 hours of a reservation. Your item will be delivered and your card will be charged the cleaning/handling fee.</span></span></p>

				                        </td>
				                    </tr>
				                </tbody></table>
				                
				            </td>
				        </tr>
				    </tbody>
				</table>



		<?php } else { // This is a next-day reservation ?>



				<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
				    <tbody class="mcnTextBlockOuter">
				        <tr>
				            <td valign="top" class="mcnTextBlockInner">
				                
				                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
				                    <tbody><tr>
				                        
				                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
				                        
				                            <p class="null" style="text-align: center;line-height:2.4;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">Your reservation of<br>
				<span style="color: #B98076;font-size: 18px;text-transform: uppercase;"><?php echo get_field('dress_designer', $dress_id); ?><br>
				<?php echo get_field('dress_description', $dress_id); ?></span><br>
				will be delivered&nbsp;tomorrow.</span></span></span></p>

				                        </td>
				                    </tr>
				                </tbody></table>
				                
				            </td>
				        </tr>
				    </tbody>
				</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
				    <tbody class="mcnDividerBlockOuter">
				        <tr>
				            <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 18px 18px 48px;">
				                <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="70%" style="border-top-width: 1px;border-top-style: solid;border-top-color: #4A443E; margin:0 auto;">
				                    <tbody><tr>
				                        <td>
				                            <span></span>
				                        </td>
				                    </tr>
				                </tbody></table>
				<!--            
				                <td class="mcnDividerBlockInner" style="padding: 18px;">
				                <hr class="mcnDividerContent" style="border-bottom-color:none; border-left-color:none; border-right-color:none; border-bottom-width:0; border-left-width:0; border-right-width:0; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
				-->
				            </td>
				        </tr>
				    </tbody>
				</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
				    <tbody class="mcnTextBlockOuter">
				        <tr>
				            <td valign="top" class="mcnTextBlockInner">
				                
				                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
				                    <tbody><tr>
				                        
				                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
				                        
				                            <p class="null" style="text-align: center;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">Please have it ready to pick up on the morning of <strong>[Booking End]</strong>.</span></span></span></p>

				                        </td>
				                    </tr>
				                </tbody></table>
				                
				            </td>
				        </tr>
				    </tbody>
				</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
				    <tbody class="mcnTextBlockOuter">
				        <tr>
				            <td valign="top" class="mcnTextBlockInner">
				                
				                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
				                    <tbody><tr>
				                        
				                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
				                        
				                            <p class="null" style="text-align: center;">Please also be aware that, out of respect for the sartorial rights of all our members, failure to have an item ready for pick up on the designated date will result in a $100/day automatic late fee. This reservation cannot be cancelled.</p>

				                        </td>
				                    </tr>
				                </tbody></table>
				                
				            </td>
				        </tr>
				    </tbody>
				</table>





		<?php } ?>

	<?php } else if ( $product->product_type == "simple" && $product_type == "share"  ) { // This is a share purchase ?>



		<?php $dress_id = CC_Controller::get_dress_for_product( $product->id, "share" ); ?>

		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
		    <tbody class="mcnTextBlockOuter">
		        <tr>
		            <td valign="top" class="mcnTextBlockInner">
		                
		                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
		                    <tbody><tr>
		                        
		                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
		                        
		                            <p class="null" style="text-align: center;line-height:2.4;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">Thank you for your purchase of a share in <br>
		<span style="color: #B98076;font-size: 18px;text-transform: uppercase;"><?php echo get_field('dress_designer', $dress_id) ?><br>
		<?php echo get_field('dress_description', $dress_id) ?></span></span></span></span></p>

		                        </td>
		                    </tr>
		                </tbody></table>
		                
		            </td>
		        </tr>
		    </tbody>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
		    <tbody class="mcnDividerBlockOuter">
		        <tr>
		            <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 18px 18px 48px;">
		                <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="70%" style="border-top-width: 1px;border-top-style: solid;border-top-color: #4A443E; margin:0 auto;">
		                    <tbody><tr>
		                        <td>
		                            <span></span>
		                        </td>
		                    </tr>
		                </tbody>
		                </table>
		            </td>
		        </tr>
		    </tbody>
		</table>
		            </td>
		        </tr>
		    </tbody>
		</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
		    <tbody class="mcnTextBlockOuter">
		        <tr>
		            <td valign="top" class="mcnTextBlockInner">
		                
		                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
		                    <tbody><tr>
		                        
		                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
		                        
		                            <p class="null" style="text-align: center;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">You can manage your dress reservation at<br>
		<span style="font-size:14px"><span style="font-family:arial,helvetica neue,helvetica,sans-serif; letter-spacing:4px; text-transform:uppercase"><a href="https://couturecollective.club/closet/" target="_blank">couturecollective.club/closet</a></span></span></span></span></span></p>

		                        </td>
		                    </tr>
		                </tbody></table>
		                
		            </td>
		        </tr>
		    </tbody>
		</table>




	<?php } else { // This is a sale purchase ?>



		<?php $dress_id = CC_Controller::get_dress_for_product( $product->id, "sale" ); ?>

		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
		    <tbody class="mcnTextBlockOuter">
		        <tr>
		            <td valign="top" class="mcnTextBlockInner">
		                
		                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
		                    <tbody><tr>
		                        
		                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
		                        
		                            <p class="null" style="text-align: center;line-height:2.4;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">Thank you for your purchase of<br>
		<span style="color: #B98076;font-size: 18px;text-transform: uppercase;"><?php echo get_field('dress_designer', $dress_id) ?><br>
		<?php echo get_field('dress_description', $dress_id) ?></span></span></span></span></p>

		                        </td>
		                    </tr>
		                </tbody></table>
		                
		            </td>
		        </tr>
		    </tbody>
		</table>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnDividerBlock" style="min-width:100%;">
		    <tbody class="mcnDividerBlockOuter">
		        <tr>
		            <td class="mcnDividerBlockInner" style="min-width: 100%; padding: 18px 18px 48px;">
		                <table class="mcnDividerContent" border="0" cellpadding="0" cellspacing="0" width="70%" style="border-top-width: 1px;border-top-style: solid;border-top-color: #4A443E; margin:0 auto;">
		                    <tbody><tr>
		                        <td>
		                            <span></span>
		                        </td>
		                    </tr>
		                </tbody>
		                </table>
		            </td>
		        </tr>
		    </tbody>
		</table>
		            </td>
		        </tr>
		    </tbody>
		</table><table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
		    <tbody class="mcnTextBlockOuter">
		        <tr>
		            <td valign="top" class="mcnTextBlockInner">
		                
		                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
		                    <tbody><tr>
		                        
		                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
		                        
		                            <p class="null" style="text-align: center;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">This item will be delivered to you on <strong>[Season End Date]</strong>.</span></span></span></p>

		                        </td>
		                    </tr>
		                </tbody></table>
		                
		            </td>
		        </tr>
		    </tbody>
		</table>




	<?php } ?>

<?php } ?>



<?php do_action( 'woocommerce_email_footer' ); ?>