<?php
    
    
    namespace gedik_yatirim;
    
    
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
            
            $url       = 'https://web.gediktrader.com/v/controllers/gedikSiteLogin';
            $post_data = [
                'username'      => $username,
                'password'      => $password,
                'userType'      => $user_type,
                'local_address' => '',
            ];
            $json = $this->request($url,'POST',$post_data);
            print_r($json);
            
        }
        
    }