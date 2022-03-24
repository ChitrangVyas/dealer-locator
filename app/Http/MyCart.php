<?php 
namespace App\Http;

use Darryldecode\Cart\Cart;
use Session;

class MyCart
{

    private $session;
    function __construct() {
        $this->session = \Session();
    }

    // put for add the value in the session
    public function setFrom($cart_id,$from){        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['from_user'] = $from;
        $this->session->put($cart_id,json_encode($json));        
    }

    public function setTo($cart_id,$to){        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['to_user'] = $to;
        $this->session->put($cart_id,json_encode($json));        
    }
    
    public function setForManufacture($cart_id,$for_manufacturer){        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['for_manufacturer'] = $for_manufacturer;
        $this->session->put($cart_id,json_encode($json));        
    }

    public function setOrderNumber($cart_id,$order_number){        
        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['order_number'] = $order_number;
        
        $this->session->put($cart_id,json_encode($json));        
    }
    public function setCreatedat($cart_id,$created_at){
        $json = json_decode($this->session->get($cart_id,'{}'),true);     
        $json['created_at'] = $created_at;       
        $this->session->put($cart_id,json_encode($json));                        
    }
    public function getCreatedat($cart_id){            
        $json = json_decode($this->session->get($cart_id,'{}'),true);

        return isset($json['created_at']) ? $json['created_at'] : date('Y-m-d');
    }   

    public function setInquiryId($cart_id,$inquiry_id){        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['inquiry_id'] = $inquiry_id;
        $this->session->put($cart_id,json_encode($json));        
    }

    public function setOrderReference($cart_id,$order_id){        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['order_reference'] = $order_id;
        $this->session->put($cart_id,json_encode($json));        
    }

    public function setType($cart_id,$type){        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['type'] = $type;
        $this->session->put($cart_id,json_encode($json));        
    }

    public function setForManufacturePoTOSale($cart_id,$for_manufacturer){
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['for_manufacture_po_to_sale'] = (!empty($for_manufacturer))?$for_manufacturer:0;
        $this->session->put($cart_id,json_encode($json));
    }

    public function setOrderId($cart_id,$order_id=''){        
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        $json['order_id'] = $order_id;
        $this->session->put($cart_id,json_encode($json));
    }

    public function getFrom($cart_id){        
        $json = json_decode($this->session->get($cart_id,[]),true);
        return $json['from_user'];
    }

    public function getTo($cart_id){        
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return (!empty($json['to_user']))?$json['to_user']:'';
    }

    public function getOrderNumber($cart_id){        
        
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return !empty($json['order_number'])?$json['order_number']:'';
    }

    public function getInquiryId($cart_id){        
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return !empty($json['inquiry_id'])?$json['inquiry_id']:'';
    }

    public function getOrderReference($cart_id){        
        $json = json_decode($this->session->get($cart_id,[]),true);
        return !empty($json['order_reference'])?$json['order_reference']:'';
    }

    public function getType($cart_id){        
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return !empty($json['type'])?$json['type']:'';
    }

    public function getForManufacture($cart_id){        
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return !empty($json['for_manufacturer'])?$json['for_manufacturer']:'';
    }

    public function getAll($cart_id){
        return (!empty($this->session->get($cart_id)))?json_decode(($this->session->get($cart_id,[])),true):'';
    }

    public function getForManufacturePoTOSale($cart_id){
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return !empty($json['for_manufacture_po_to_sale'])?$json['for_manufacture_po_to_sale']:'';
    }

    public function getOrderId($cart_id){
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return !empty($json['order_id'])?$json['order_id']:'';
    }
    
    public function setProductTax($cart_id,$productTax){        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['product_tax'] = $productTax;
        $this->session->put($cart_id,json_encode($json));        
    }
    public function setTaxGroup($cart_id,$tax_group,$isNew=false){
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        if($isNew){
            $key = key($tax_group);
            $json['tax_group'][$key] = $tax_group[$key];
        }else{
            $json['tax_group'] = $tax_group;
        }
        $this->session->put($cart_id,json_encode($json));     
    }
    public function setCartTotal($cart_id,$cart_total){
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['cart_total'] = $cart_total;
        $this->session->put($cart_id,json_encode($json));
    }
    public function getSubtotalWithDiscuount($product,$cart_id,$cart){
        $subtotal = $product->getPriceSum();
        return $subtotal -= $subtotal*$this->getDiscountPerProductPercentage($product->id,$cart_id,$cart)/100;
    }
    public function getDiscountPerProductPercentage($productId,$cart_id,$cart){
        $totalDiscount=0.00;        
        if((!empty($cart_id)) && (!$cart->isEmpty())){                        
            foreach($cart->getContent() as $product){                
                if($product->id==$productId){                    
                    foreach ($product->conditions as $key=>$condition){                        
                        if(strpos($key,'Discount')!==false){                                                   
                            $totalDiscount = abs(rtrim($condition->getValue(),"%"));
                        }    
                    }
                }
            }                       
        }
        return $totalDiscount;
    }
    public function getDiscountPerProduct($productId,$cart_id,$cart){
        $totalDiscount=0.00;        
        if((!empty($cart_id)) && (!$cart->isEmpty())){                        
            foreach($cart->getContent() as $product){                
                if($product->id==$productId){                    
                    foreach ($product->conditions as $key=>$condition){                        
                        if(strpos($key,'Discount')!==false){                                                   
                            $discount_percentage = abs(rtrim($condition->getValue(),"%"));
                            $totalDiscount += ( $product->getPriceSum() * ($discount_percentage /100) );                                                
                        }    
                    }
                }
            }                       
        }
        return $totalDiscount;
    }

    public function getDiscountTotal($cart_id,$cart){
        $totalDiscount=0.00;        
        if((!empty($cart_id)) && (!$cart->isEmpty())){                        
            foreach($cart->getContent() as $product){
                foreach ($product->conditions as $key=>$condition){
                    if(strpos($key,'Discount')!==false){                    
                        $discount_percentage = abs(rtrim($condition->getValue(),"%"));
                        $totalDiscount += ( $product->getPriceSum() * ($discount_percentage /100) );                                                
                    }    
                }                
            }                       
        }
        return $totalDiscount;
    }

    public function setTaxTotal($cart_id,$cart){
        $total_tax = [];
        if((!empty($cart_id)) && (!$cart->isEmpty())){
            $conditions = [];
            $total = 0.00;
            $total_tax['Sub total'] = 0.00;
            $productTax = [];
            $cart_total = 0.00;
            foreach($cart->getContent() as $product){
                foreach ($product->conditions as $key=>$condition){
                    if(strpos($key,'tax')!==false){                    
                        $tax_percentage = rtrim($condition->getValue(),"%");
                        $product_tax = ( $this->getSubtotalWithDiscuount($product,$cart_id,$cart) * ($tax_percentage /100) );
                        $productTax[$product->id][] = ["tax_id"=>$condition->getAttributes()['tax_id'],"tax"=>($product_tax)];
                        if(!in_array($condition->getName(),$conditions)){
                            $conditions[] = $condition->getName();
                            $total_tax[$condition->getName()] = 0.00;
                        }
                        if((in_array($condition->getName(),$conditions))){    
                            $total_tax[$condition->getName()] = $total_tax[$condition->getName()] + ($product_tax);
                            $cart_total += $product_tax;
                        }
                    }    
                }
                $total += ($product->getPriceSum()-$this->getDiscountPerProduct($product->id,$cart_id,$cart));
            }
            $cart_total += $total;
            $this->setProductTax($cart_id,$productTax);        
            
            $this->setCartTotal($cart_id,$cart_total);
            $total_tax['Sub total'] = $total;
            $json = json_decode($this->session->get($cart_id,'{}'),true);
            $json['total_tax'] = $total_tax;
            
            $this->session->put($cart_id,json_encode($json));
        }
    }

    public function getTaxTotal($cart_id){
            $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
            return !empty($json['total_tax'])?$json['total_tax']:'';
    }

    public function getProductTax($cart_id){
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return !empty($json['product_tax'])?$json['product_tax']:'';
    }

    public function getTaxGroup($cart_id){        
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return !empty($json['tax_group'])?$json['tax_group']:'';      
    }

    public function getCartTotal($cart_id){        
        $json = (!empty($this->session->get($cart_id)))?json_decode($this->session->get($cart_id,[]),true):'';
        return !empty($json['cart_total'])?($json['cart_total']):'';
    }

    public function clear($cart_id){
        if ($this->session->has($cart_id)){
            return $this->session->forget($cart_id);
        }
    }

    public function removeItem($cart_id,$item){
        $cart = $this->getAll($cart_id);
        if(!empty($cart)){
            if(is_array($item)){
                foreach($item as $key => $value){
                    foreach ($value as $val){
                        if(in_array($key,$cart)){
                            $keys = array_keys($cart[$key]);
                            if(in_array($val,$keys)){
                                unset($cart[$key][$val]);
                            }
                        }
                    }
                }
            }else{
                unset($cart[$item]);
            }
            $this->session->put($cart_id,json_encode($cart));
        }
    }
    public function setInquires($cart_id,$inquiries){        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['inquiries'] = $inquiries;
        $this->session->put($cart_id,json_encode($json));        
    }
    public function getInquires($cart_id){        
        $json = json_decode($this->session->get($cart_id,[]),true);        
        return isset($json['inquiries']) ? $json['inquiries'] : [];
    }
}