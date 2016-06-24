<?php
require_once('stripe-php/init.php');

$stripe = array(
  secret_key      => 'sk_live_foIJKEZYedxqdDEWtPOJKsuT',
  publishable_key => 'pk_live_gFRAIlfZg9gkNgJx2uRLIDjH'
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>