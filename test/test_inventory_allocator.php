<?php

$order_stream = array(
	'{"Header": 1, "Lines": [{"Product": "A", "Quantity": "1"}, {"Product": "C", "Quantity": "1"}]}',
	'{"Header": 2, "Lines": [{"Product": "E", "Quantity": "5"}]}',
	'{"Header": 3, "Lines": [{"Product": "D", "Quantity": "4"}]}',
	'{"Header": 4, "Lines": [{"Product": "A", "Quantity": "1"}, {"Product": "C", "Quantity": "1"}]}',
	'{"Header": 5, "Lines": [{"Product": "B", "Quantity": "3"}]}',
	'{"Header": 6, "Lines": [{"Product": "D", "Quantity": "4"}]}'
);

$inventory = array("A" => 2, "B" => 3, "C" => 1, "D" => 0, "E" => 0);

include( "../model/inventory_allocator.php" );

?>
