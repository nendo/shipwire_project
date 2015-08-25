<?php

include_once( "../controller/InventoryAllocator.php" );

$allocator = new InventoryAllocator($order_stream, $inventory);
if ($allocator != NULL) {
	$allocator->process_orders();
}

?>