<?php
require_once('/lib/Stripe.php');

$stripe = array(
  "secret_key"      => "sk_test_ZkIx86MArO0aB9nnFWI5o8jM",
  "publishable_key" => "pk_test_R7riQBeDbUwT2MKZ2t0SoHny"
);

Stripe::setApiKey($stripe['secret_key']);
?>
