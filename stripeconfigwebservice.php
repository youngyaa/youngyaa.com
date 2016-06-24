<?php
require_once('stripe-php/init.php');
$stripe = array('secret_key'=>'sk_test_cEPSQ6ogxRZnNoHjNMT7UlSt','publishable_key'=>'pk_test_mvZDnzJdrGdYIDr9U7SBXOac');

\Stripe\Stripe::setApiKey($stripe['secret_key']);


?>