<?php
    
    
    namespace gedik_yatirim;
    
    class gedik_transactions extends gedik_request{
        
        public $username;
        public $password;
        public $functions;
        
        function __construct($username, $password, $functions = null){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }
        
        public function buy($symbol = '', $lot = 0){
            $trade = $this->get_live_trade_sanpsshot($symbol);
            if(isset($trade->last)){
                return $this->place_order('BUY',$symbol, $lot, $trade->last);
            }
            return false;
        }
        
        public function sell($symbol = '', $lot = 0){
            $trade = $this->get_live_trade_sanpsshot($symbol);
            if(isset($trade->last)){
                return $this->place_order('SELL',$symbol, -$lot, $trade->last);
            }
            return false;
        }
        
        public function get_live_trade_sanpsshot($symbol = null){
            $url       = 'https://web.gediktrader.com/v/controllers/getLiveTradeSnapshot';
            $post_data = [
                'symbol' => $symbol,
            ];
            $headers   = [
                'X-Atmosphere-tracking-id' => $this->get_wss_id(),
            ];
            $json      = $this->request($url, 'POST', $post_data, $headers);
            if($json['status'] == 'ok'){
                return json_decode($json['body']);
            }
            else{
                return $json;
            }
        }
        
        public function place_order($type = 'BUY', $symbol = null, $lot = 1, $limit_price = 1){
            $url       = 'https://web.gediktrader.com/v/controllers/fx/placeOrder';
            $data      = [
                'additionalParameters'   => [
                    'timeInForce'   => 'KTR',
                    'orderDuration' => 'DAY',
                ],
                'derivative'             => false,
                'effect'                 => 'OPENING',
                'equity'                 => true,
                'globalOrderTimeInForce' => 'DAY',
                'instrumentId'           => $this->get_symbol_id($symbol),
                'limitPrice'             => $limit_price,
                'orderSide'              => $type,
                'orderType'              => 'LIMIT',
                'quantity'               => $lot,
                'releatedOrderChainId'   => 0,
                'requestId'              => 'gwt-uid-2696-a6774017-21a8-4f3b-bd7a-d7bbe28853a7',
                'timeInForce'            => 'DAY',
                'vendor'                 => 'GEDIK',
            ];
            $post_data = [
                'order' => json_encode($data),
            ];
            $headers   = [
                'X-Atmosphere-tracking-id' => $this->get_wss_id(),
            ];
            $json      = $this->request($url, 'POST', $post_data, $headers);
            if($json['status'] == 'ok'){
                return json_decode($json['body']);
            }
            else{
                return $json;
            }
        }
        
        public function get_symbol_id($symbol = null){
            
            if($symbol != null){
                
                $symbol_split = explode(':', $symbol);
                
                $url       = 'https://web.gediktrader.com/v/controllers/requestSuggestWithType';
                $post_data = [
                    'prefix'          => $symbol_split[0],
                    'instrumentTypes' => '[]',
                ];
                $json      = $this->request($url, 'POST', $post_data);
                if($json['status'] == 'ok'){
                    $body   = json_decode($json['body']);
                    $result = null;
                    foreach($body as $sym){
                        if($sym->name == $symbol){
                            $result = $sym;
                            break;
                        }
                    }
                    return $result->id;
                }
                
            }
            
            return false;
        }
        
    }