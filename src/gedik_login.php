<?php
    
    
    namespace gedik_yatirim;
    
    class gedik_login extends gedik_request{
        
        public $username;
        public $password;
        public $functions;
        
        function __construct($username, $password, $functions = null){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }
    
        public function login($username = null, $password = null, $user_type = 'VIEWER'){
        
            $username = $username??$this->username;
            $password = $password??$this->password;
        
            $cache = $this->cache('sessionid');
            if(!$cache){
                $url       = 'https://web.gediktrader.com/v/controllers/gedikSiteLogin';
                $post_data = [
                    'username' => $username,
                    'password' => $password,
                    'userType' => $user_type,
                ];
                $json      = $this->request($url, 'POST', $post_data);
                $body      = json_decode($json['body']);
                if($body->loginInfoTO->status){
                    $this->cache($username, $body->loginInfoTO->user);
                    $this->cache('sessionid', [$body->loginInfoTO->sessionId]);
                    $this->functions->websocket->ws_login($body->loginInfoTO->sessionId);
                    return [$body->loginInfoTO->sessionId];
                }
            }
            else{
                //$this->functions->websocket->ws_login($cache[0]);
                return $cache;
            }
            return false;
        }
        
        public function wss_login($session_id = null){
            if($session_id != null){
                $wss = $this->functions->websocket->ws_login($session_id);
                return $wss;
            }
            return false;
        }
    }