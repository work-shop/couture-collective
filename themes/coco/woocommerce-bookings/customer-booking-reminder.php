<?php
/**
 * Customer booking reminder email
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<?php $dress_id = CC_Controller::get_dress_for_product($booking->get_product()->id, "rental"); ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                
                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
                        
                            <p class="null" style="text-align: center;line-height:2.4;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">This is a reminder that your reservation of<br>
<span style="color: #B98076;font-size: 18px;text-transform: uppercase;"><?php echo get_field( "dress_designer", $dress_id ); ?><br>
<?php echo get_field( "dress_description", $dress_id ); ?></span><br>
<span style="font-family:georgia,times,times new roman,serif">will be delivered on <strong><?php echo date( 'F jS, Y', strtotime( CC_Controller::get_selected_date($booking->id) ) ); ?></strong>.</span></span></span></span></p>

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
                        
                            <p class="null" style="text-align: center;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">Please have it ready for pickup on&nbsp;<strong><?php echo date( "F d Y", strtotime( CC_Controller::get_selected_date($booking->id) . " -1 days" ) ); ?></strong>.</span></span></span></p>

                        </td>
                    </tr>
                </tbody></table>
                
            </td>
        </tr>
    </tbody>
</table>

<?php do_action( 'woocommerce_email_footer' ); ?>