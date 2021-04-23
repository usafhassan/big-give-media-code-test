<?php

/**
 * @author: Yousaf Hassan
 * @date: 04/23/2021
 * @desc: This file contains the main classes and config for proof of concept
 */

 require_once __DIR__.'/config.php';

 /**
  * Product class to keep information related to product
  */
  
 class Product {

   // Product attributes
   private $name;
   private $code;
   private $price;
   
   // Init product data
   function __construct(String $name, String $code, Float $price) {
      $this->name       = $name;
      $this->code       = $code;
      $this->price      = $price;
   }

   // **********  Attribute getters ***************
   public function getName() {
      return $this->name;
   }

   public function getCode() {
      return $this->code;
   }

   public function getPrice() {
      return $this->price;
   }
   
}

/**
  * Main class that holds the project and its configuration bootstrapping
  */

class Acme {

   protected $productsInventory;
   protected $deliveryRules;
   protected $offers;
   
   function __construct(Array $products, Array $deliveryRules, Array $offers) {
      $this->productsInventory   = $products;
      $this->deliveryRules       = $this->sortDeliveryRules($deliveryRules);
      $this->offers              = $offers;
   }

   /**
    * This method makes sure the rules are applied properly no matter what the sequence of delivery config is
    */
   private function sortDeliveryRules ($rules) : Array {
      usort($rules, function($a, $b) {
         return $a['amountLimit'] <=> $b['amountLimit'];
     });
     return $rules;
   }

   /**
    * Method to apply special offers on the products
    * @param products - Array of products in basket
    * @param totalCost - Total cost before shipment
    */
   protected function applyOffers($products, $totalCost) : Float {

      // Check if there is any offer on a product
      foreach($products as $product) {
         // Iterate over offers
         foreach($this->offers as $offer) {
            
            /**
             * Check if a product matches the products (in basket)
             * Also check if quantity is greater than 1
             */
            if($offer['itemCode'] === $product['product']->getCode() && $product['qty'] >= $offer['qtyApplicable']) {

               
               // This formula is the key to apply offer discount to every couple in the basket instead of just the second
               $discountCount    = floor($product['qty'] / $offer['qtyApplicable']); 
               
               // Check if the offer amount is a fixed price or percentage
               if( $offer['isPercentage'] ) {

                  $offerDiscount = $product['product']->getPrice() * ($offer['offerDiscount'] * ($discountCount) / 100);
                  $totalCost     = $totalCost - ( $offerDiscount );

               } else {

                  $offerDiscount = $product['product']->getPrice() - $offer['offerDiscount'];
                  $totalCost     = $totalCost - ( $offerDiscount );

               }

            } // Offer match condition ends here

         } // Nestd offers loop ends here

      } // Products loop ends here

      return $totalCost;
   }

}

/**
 * Basket that acts as a shopping cart
 */
class Basket extends Acme {

   private $selectedProducts = [];

   function __construct(Array $products, Array $deliveryRules, Array $offers) {
      parent::__construct($products, $deliveryRules, $offers);
   }

   /**
    * Add product to basket
    * @param productCode
    */
   public function add(String $productCode) : void {
      /**
       * Increase the quantity if product is already there,
       * otherwise just add a new product to basket
       */
      $index = $this->isAlreadyAdded($productCode);
      if( $index !== false ) {
         $this->selectedProducts[$index]['qty'] += 1;
      } else {
         
         // Get product by code and add it to basket
         foreach($this->productsInventory as $product) {

            if( $productCode === $product->getCode() ) {
                  $this->selectedProducts[] = [
                     'product'      => $product,
                     'qty'          => 1
                  ];
            }
   
         }
      }
   }

   /**
    * This method checks if a product is already in the baset
    * returns index if found and returns false if not found
    */
   private function isAlreadyAdded($productCode) {
      foreach( $this->selectedProducts as $index => $product ) {
         if( $productCode === $product['product']->getCode() ) {
            return $index;
         }
      }
      return false;
   }

   /**
    * Get current products in the basket
    */
   private function getSelectedProductCodes() {
      return array_map(function($product) {
         return $product['product']->getCode().'('.$product['qty'].')';
      }, $this->selectedProducts);
   }

   /**
    * Internal helper function that takes care of the total price billable to the customer
    */
   private function getTotalCost() : Float {
      $grossTotal     = 0;
      foreach( $this->selectedProducts as $product ) {
         $grossTotal     += $product['product']->getPrice() * $product['qty'];
      }
      $grossTotal     = parent::applyOffers($this->selectedProducts, $grossTotal);
      $shippingCost  = $this->getShippingCost($grossTotal);
      return number_format($grossTotal + $shippingCost, 2);
   }

   /**
    * Calculate shipping fe
    * @param totalCost - Float, total cost of the products in basket
    */
   private function getShippingCost($totalCost) : Float {
      $shippingCost     = 0; // free delivery
      foreach($this->deliveryRules as $rule) {
         if ($totalCost < $rule['amountLimit']) {
            return $rule['cost'];
         }
      }
      return $shippingCost;
   }

   public function checkout(): void {
      echo '<pre>'. implode( $this->getSelectedProductCodes(), ', ' ) . '        '. CURRENCY_SYMBOL . $this->getTotalCost() . '</pre>';
      // Empty the basket after each checkout
      $this->selectedProducts = [];
   }

}

?>