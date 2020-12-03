<?php

// check recurrent payments
header('Content-Type: application/json');

if (empty($global['systemRootPath'])) {
    $global['systemRootPath'] = '../../';
}
require_once $global['systemRootPath'] . 'videos/configuration.php';

_error_log("StripeINTENT Start");
$plugin = AVideoPlugin::loadPluginIfEnabled("YPTWallet");
$walletObject = AVideoPlugin::getObjectData("YPTWallet");
$stripe = AVideoPlugin::loadPluginIfEnabled("StripeYPT");
$stripeObject = AVideoPlugin::getObjectData("StripeYPT");

$obj = new stdClass();
$obj->error = true;
$obj->msg = "";
$obj->client_secret = "";

if (!User::isLogged()) {
    $obj->msg = "Please login first";
    die(json_encode($obj));
}

if (empty($stripe)) {
    $obj->msg = "Stripe Plugin Disabled";
    die(json_encode($obj));
}
if (empty($plugin)) {
    $obj->msg = "Wallet Plugin Disabled";
    die(json_encode($obj));
}

$value = floatval($_REQUEST['value']);

if (empty($value)) {
    $obj->msg = "Value is empty";
    die(json_encode($obj));
}

$currency = $walletObject->currency;

$intent = $stripe->getIntent($value, $currency, @$_REQUEST['description']);

$obj->client_secret = $intent->client_secret;
$obj->error = empty($obj->client_secret);
die(json_encode($obj));
?>