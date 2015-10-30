<?
/*
	Template Name: Test-Email
*/
?>
<?php

$order_id = 985;

// the email we want to send
$email_class = 'WC_Email_Customer_Processing_Order';

// load the WooCommerce Emails
$wc_emails = new WC_Emails();

$emails = $wc_emails->get_emails();

// select the email we want & trigger it to send
$new_email = $emails[$email_class];

$new_email->trigger($order_id);

// show the email content

echo $new_email->get_content();


?>
