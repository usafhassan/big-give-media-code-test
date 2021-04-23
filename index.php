<?php

/**
 * @author: Yousaf Hassan
 * @date: 04/23/2021
 * @desc: This widget is a proof of concept product basket for Acme Widget Co and released under open licensing
 */

require_once __DIR__.'/core.php';

// Create 3 products (dummy data entry)
$products      = [
   new Product('Red Widget', 'R01', 32.95),
   new Product('Green Widget', 'G01', 24.95),
   new Product('Blue Widget', 'B01', 7.95)
];

// Our hard coded offers array
$offers        = [[
   'title'           => 'Buy one red widget, get the second half pric',
   'itemCode'        => 'R01',
   'qtyApplicable'   => 2,
   'offerDiscount'   => '50',
   'isPercentage'    => true,
]];

$basket  = new Basket($products, DELIVERY_RULES, $offers);

// Test Sample 1
$basket->add('B01');
$basket->add('G01');

$basket->checkout();

/** New customer can pick a new basket or use the existing one, we will use existing one **/
// - Test Sample 2
$basket->add('R01');
$basket->add('R01');

$basket->checkout();

// Test Sample 3
$basket->add('R01');
$basket->add('G01');

$basket->checkout();

// Test Sample 4
$basket->add('B01');
$basket->add('B01');
$basket->add('R01');
$basket->add('R01');
$basket->add('R01');
$basket->add('R01');

$basket->checkout();


?>