<?php

// Tested on PHP 5.2, 5.3

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('Stripe needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Stripe needs the JSON PHP extension.');
}
if (!function_exists('mb_detect_encoding')) {
  throw new Exception('Stripe needs the Multibyte String PHP extension.');
}

// Stripe singleton
require('Stripe/Stripe.php');

// Utilities
require('Stripe/Util.php');
require('Stripe/Util/Set.php');

// Errors
require('Stripe/Error.php');
require('Stripe/ApiError.php');
require('Stripe/ApiConnectionError.php');
require('Stripe/AuthenticationError.php');
require('Stripe/CardError.php');
require('Stripe/InvalidRequestError.php');

// Plumbing
require('Stripe/Object.php');
require('Stripe/ApiRequestor.php');
require('Stripe/ApiResource.php');
require('Stripe/SingletonApiResource.php');
require('Stripe/AttachedObject.php');
require('Stripe/List.php');

// Stripe API Resources
require('Stripe/Account.php');
require('Stripe/Card.php');
require('Stripe/Balance.php');
require('Stripe/BalanceTransaction.php');
require('Stripe/Charge.php');
require('Stripe/Customer.php');
require('Stripe/Invoice.php');
require('Stripe/InvoiceItem.php');
require('Stripe/Plan.php');
require('Stripe/Token.php');
require('Stripe/Coupon.php');
require('Stripe/Event.php');
require('Stripe/Transfer.php');
require('Stripe/Recipient.php');
