# Glovo Business API PHP Integration Package

Create, retrieve and track your Glovo orders trough their Business API.

## Requirements

PHP 7.2 and later.

## Install Package Via Composer

```bash
$ composer require osenco/glovo
```

To use the Glovo API in your project, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

## Dependencies

The bindings require the following extensions in order to work properly:

-   [`curl`](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer
-   [`json`](https://secure.php.net/manual/en/book.json.php)
-   [`mbstring`](https://secure.php.net/manual/en/book.mbstring.php) (Multibyte String)

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

## Getting Started

Create an account in Glovo (it can be created from the App). This api **needs a credit card associated to your account**. You can add one from your app and it will be used automatically for any order. In order to get your API credentials you should login in the desired environment and go to _Credentials_ section on your profile.

-   [B2B Production](https://business.glovoapp.com/dashboard/profile)
-   [B2B Sandbox/Test](https://business.testglovo.com/dashboard/profile)

Example ApiKey & ApiSecret:

```php
$apiKey = '125238463972712';
$apiSecret = '081f8c9680d457a088b4413a62ddf84c';
```

Sample usage might look smething like this:

```php
<?php

include 'vendor/autoload.php';

use Osen\Glovo\Service;
use Osen\Glovo\Exception;
use Osen\Glovo\Models\Order;
use Osen\Glovo\Models\Address;

// get credentials on https://business.glovoapp.com/dashboard/profile or https://business.testglovo.com/dashboard/profile
$api = new Api( $apiKey, $apiSecret);
$api->sandbox_mode( true );

$sourceDir = new Address( Address::TYPE_PICKUP, -34.919861, -57.919027, "Diag. 73 1234", "1st floor" );
$destDir = new Address( Address::TYPE_DELIVERY, -34.922945, -57.990177, "Diag. 73 75", "3A");

$order = new Order();
$order->setDescription( "1 big hammer" );
$order->setAddresses( [$sourceDir, $destDir] );
//$order->setScheduleTime( ( new \DateTime( '+1 hour' ) )->setTime( 19, 0 ) );

try {
    $orderEstimate = $api->estimateOrderPrice( $order );
    echo "Estimate: {$orderEstimate['total']['amount']}{$orderEstimate['total']['currency']} \n";
} catch(Exception $e){
    echo $e->getMessage();
}

try {
    $orderInfo = $api->createOrder( $order );
    echo "Order created, ID: {$orderInfo['id']}, state: {$orderInfo['state']} \n";
} catch(Exception $e){
    echo $e->getMessage();
}

```

See full example in [example.php](example.php)

## Update Certificates

In order to update the CA Root certificates you can run:

```bash
$ chmod +x ./update_certs.php
$ ./update_certs.php
```

## Documentation

You can read [the Glovo B2B API docs][https://api-docs.glovoapp.com/b2b/index.html#introduction].
