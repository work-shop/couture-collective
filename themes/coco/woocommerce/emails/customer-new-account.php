<?php
/**
 * Customer new account email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="mcnTextBlock">
    <tbody class="mcnTextBlockOuter">
        <tr>
            <td valign="top" class="mcnTextBlockInner">
                
                <table align="left" border="0" cellpadding="0" cellspacing="0" width="600" class="mcnTextContentContainer">
                    <tbody><tr>
                        
                        <td valign="top" class="mcnTextContent" style="padding-top:9px; padding-right: 18px; padding-bottom: 9px; padding-left: 18px;">
                        
                            <p class="null" style="text-align: center;line-height:2.4;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">Thank you for signing up for an account with Couture Collective!</span></span></p>

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
                        
                            <p class="null" style="text-align: center;"><span style="font-size:16px"><span class="mc-toc-title"><span style="font-family:georgia,times,times new roman,serif">You will be notified once your account is approved.</span></span></span></p>

                        </td>
                    </tr>
                </tbody></table>
                
            </td>
        </tr>
    </tbody>
</table>

<?php do_action( 'woocommerce_email_footer' ); ?>