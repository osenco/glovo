<?php

include 'vendor/autoload.php';

use Osen\Glovo\Exception;
use Osen\Glovo\Models\Address;
use Osen\Glovo\Models\Order;
use Osen\Glovo\Service;

$apiKey    = "158634433098486";
$apiSecret = "d244ec3f6d5b4983a68716e4df443f6b";
$api       = new Service($apiKey, $apiSecret);
// $api->sandbox_mode( true );

$source      = new Address(Address::TYPE_PICKUP, -34.919861, -57.919027, "Diag. 73 1234", "1st floor");
$destination = new Address(Address::TYPE_DELIVERY, -34.922945, -57.990177, "Diag. 73 75", "3A");

$order = new Order();
$order->setDescription("1 big hammer");
$order->setAddresses([$source, $destination]);
// $order->setScheduleTime( ( new \DateTime( '+1 hour' ) )->setTime( 19, 0 ) );
try {
	$orderEstimate = $api->estimateOrderPrice($order);
	echo "Estimate: {$orderEstimate['total']['amount']}{$orderEstimate['total']['currency']} \n";
} catch (Exception $e) {
	echo $e->getMessage();
}

try {
	$orderInfo = $api->createOrder($order);
	echo "Order created, ID: {$orderInfo['id']}, state: {$orderInfo['state']} \n";
} catch (Exception $e) {
	echo $e->getMessage();
}

$order_id  = $orderInfo['id'];
$laststate = $orderInfo['state'];

//Track Order
while ($laststate !== Order::STATE_DELIVERED) {
	$info = $api->retrieveOrder($order_id);

	if ($info['state'] !== $laststate) {
		$laststate = $info['state'];

		if ($laststate === Order::STATE_ACTIVE) {
			$courier_info = $api->getCourierContact($order_id);
			echo "Your courier is {$courier_info['courier']}, Phone number {$courier_info['phone']}\n";
		} else {
			echo "Current order status is $laststate \n";
		}
	}

	if ($laststate === Order::STATE_ACTIVE) {
		$tracking = $api->getOrderTracking($order_id);
		echo "Current order position is {$tracking['lat']} lattitude and {$tracking['lon']} longitude\n";
	}

	sleep(45);
}
