<?php

class InventoryAllocator {

	private $order_stream = NULL;
	private $inventory = NULL;
	private $output_listing = NULL;
	private $inventory_remaining;
	private $order_in;
	private $backorder;
	private $ordered;

	public function __construct( $the_order_stream, $the_inventory) {
		
		$this->order_stream = $the_order_stream;
		$this->inventory = $the_inventory;
		$this->output_listing = "";
		$this->inventory_remaining = false;
	}
	
	private function inventory_remains() {
		$this->inventory_remaining = false;
		// Iterate over the inventory until we confirm that it contains at least 1 item
		reset($this->inventory);
		$quantity = next($this->inventory);
		while(($this->inventory_remaining == false) && ($quantity !== false)) {

			if ($quantity > 0) {
				$this->inventory_remaining = true;
			}

			$quantity = next($this->inventory);
		}
		
		return $this->inventory_remaining;
	}
	
	// handle an individual line entry, and update the $order_in, $backorder, and $ordered arrays accordingly
	private function process_line($the_line) {

		$this->order_in[$the_line->Product] += $the_line->Quantity;
		
		if ($the_line->Quantity > 0 && $the_line->Quantity < 6) {
			if ($this->inventory[$the_line->Product] >= $the_line->Quantity) {
				$this->inventory[$the_line->Product] -= $the_line->Quantity;
				$this->ordered[$the_line->Product] += $the_line->Quantity;
			} else { // If a line cannot be satisfied, it should not be allocated. Rather, it should be  backordered
				$this->backorder[$the_line->Product] += $the_line->Quantity;
			}

		} else {
			echo "invalid order\n";
			// Add invalid order handler here, once the expected behavior has been defined
		}
	}

	// handle all the lines in an individual order
	private function process_order( $the_order ) {

		$this->order_in = array("A" => 0, "B" => 0, "C" => 0, "D" => 0, "E" => 0 );
		$this->backorder = array("A" => 0, "B" => 0, "C" => 0, "D" => 0, "E" => 0 );
		$this->ordered = array("A" => 0, "B" => 0, "C" => 0, "D" => 0, "E" => 0 );
		
		$order_object = json_decode($the_order);

		foreach( $order_object->Lines as $line) {
			
			$this->process_line($line);
		}
		
		$header_id = $order_object->Header;
		$order_string = "$header_id: ";
		foreach($this->order_in as $product => $quantity) {
			$order_string .= "$quantity";
			if (strcmp($product, "E") != 0) {
				$order_string .= ",";
			} else {
				$order_string .= "::";
			}
		}

		foreach($this->ordered as $product => $quantity) {
			$order_string .= "$quantity";
			if (strcmp($product, "E") != 0) {
				$order_string .= ",";
			} else {
				$order_string .= "::";
			}
		}

		foreach($this->backorder as $product => $quantity) {
			$order_string .= "$quantity";
			if (strcmp($product, "E") != 0) {
				$order_string .= ",";
			}
		}
		
		return $order_string;
	}
	
	// handle all the orders
	public function process_orders() {
		
		if($this->order_stream != NULL && $this->inventory != NULL && $this->inventory_remains()) {
			foreach($this->order_stream as $order) {
				if ($this->inventory_remains() == true) {
					$output_listing .= $this->process_order($order) . "\n";
				} else {
					break;
				}
			}
			echo "\n$output_listing";
		} else {
			echo "SOMETHING IS AMISS\n";
		}
	}

} // class InventoryAllocator
?>