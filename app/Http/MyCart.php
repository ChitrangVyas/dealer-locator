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

    public function getFrom($cart_id){        
        $json = json_decode($this->session->get($cart_id,[]),true);
        return $json['from_user'];
    }

    public function getTo($cart_id){        
        $json = json_decode($this->session->get($cart_id,[]),true);
        return $json['to_user'];
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
        return json_decode($this->session->get($cart_id,[]),true);
    }

    public function clear($cart_id){
        if ($this->session->has($cart_id)){
            return $this->session->forget($cart_id);
        }
    }

}