<?php
    
    
    namespace gedik_yatirim;
    
    require "gedik_request.php";
    
    class gedik_yatirim extends gedik_request{
        
        public $username = null;
        public $password = null;
        
        public function __construct($username = null, $password = null){
            
            $this->username = $username;
            $this->password = $password;
            
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
                if($body->loginStatusTO->status){
                    foreach($json['headers']['Set-Cookie'] as $cookie){
                        if($this->start_with($cookie, 'JSESSIONID')){
                            preg_match('|JSESSIONID=(.*?);|is', $cookie, $session_id);
                            $this->cache('sessionid', [$session_id[1]]);
                            break;
                        }
                    }
                    return [$session_id[1]];
                }
            }else{
                return $cache;
            }
            return false;
        }
        
    }