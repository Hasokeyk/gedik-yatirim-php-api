<?php
    
    
    namespace gedik_yatirim;
    
    require "gedik_request.php";
    require "gedik_login.php";
    require "gedik_transactions.php";
    
    //require "gedik_websocket.php";
    
    class gedik_yatirim{
        
        public $functions = null;
        //public $websocket    = null;
        public $request      = null;
        public $login        = null;
        public $transactions = null;
        
        public function __construct($username = null, $password = null){
            
            $this->functions = (object) [
                'request'      => new gedik_request($username, $password, $this->functions),
                'login'        => new gedik_login($username, $password, $this->functions),
                'transactions' => new gedik_transactions($username, $password, $this->functions),
                'websocket'    => new gedik_websocket($username, $password, $this->functions),
            ];
            
            $this->request      = new gedik_request($username, $password, $this->functions);
            $this->login        = new gedik_login($username, $password, $this->functions);
            $this->transactions = new gedik_transactions($username, $password, $this->functions);
            $this->websocket    = new gedik_websocket($username, $password, $this->functions);
            
        }
        
    }