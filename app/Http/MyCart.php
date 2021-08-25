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
        $json['from'] = $from;
        $this->session->put($cart_id,json_encode($json));        
    }

    public function setTo($cart_id,$to){        
        $json = json_decode($this->session->get($cart_id,'{}'),true);
        $json['to'] = $to;
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

    public function getFrom($cart_id){        
        $json = json_decode($this->session->get($cart_id,[]),true);
        return $json['from'];
    }

    public function getTo($cart_id){        
        $json = json_decode($this->session->get($cart_id,[]),true);
        return $json['to'];
    }

    public function getOrderNumber($cart_id){        
        $json = json_decode($this->session->get($cart_id,[]),true);
        return !empty($json['order_number'])?$json['order_number']:'';
    }

    public function getInquiryId($cart_id){        
        $json = json_decode($this->session->get($cart_id,[]),true);
        return !empty($json['inquiry_id'])?$json['inquiry_id']:'';
    }
}