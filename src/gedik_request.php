<?php
    
    namespace gedik_yatirim;
    
    use GuzzleHttp\Exception\GuzzleException;
    
    class gedik_request{
        
        public $headers;
        
        public $cache_path   = (__DIR__).'/cache/';
        public $cache_prefix = 'gedik';
        public $cache_time   = 10; //Minute
        
        public $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.72 Safari/537.36';
        
        public $username;
        public $password;
        public $functions;
        
        function __construct($username, $password, $functions = null){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }
        
        public function create_cookie($array = false, $session_id = true){
            
            $cookies_array = [//'mid' => 'YB2r4AABAAERcl5ESNxLjr_tt4Q5',
            ];
            
            if($session_id === true){
                $cookies_array['JSESSIONID'] = $this->get_session_id();;
            }
            
            if($array == false){
                $cookies = '';
                foreach($cookies_array as $cookie => $value){
                    $cookies .= $cookie.'='.$value.'; ';
                }
                return $cookies;
            }
            
            return $cookies_array;
            
        }
        
        public function get_session_id($username = null){
            
            $username       = $username??$this->username;
            $this->username = $username;
            
            $cookie = $this->cache('sessionid');
            if($cookie == false){
                $session_id = 0;
            }
            else{
                $session_id = $cookie[0];
            }
            
            return $session_id;
        }
        
        public function cache($name, $desc = false, $json = false){
            
            if(!file_exists($this->cache_path.$this->username)){
                mkdir($this->cache_path.$this->username, 777);
            }
            
            $cache_file_path = $this->cache_path.$this->username.'/';
            $cache_file      = realpath($cache_file_path.($name.'.json'));
            
            if(file_exists($cache_file) and time() <= strtotime('+'.$this->cache_time.' minute', filemtime($cache_file))){
                return json_decode(file_get_contents($cache_file));
            }
            
            else if($desc !== false){
                if($json == true){
                    file_put_contents($cache_file, $desc);
                }
                else{
                    file_put_contents($cache_file, json_encode($desc));
                }
                return $desc;
            }
            else{
                return false;
            }
        }
        
        public function request($url = '', $type = 'GET', $data = null, $header = null, $cookie = null, $user_cookie = true){
            
            if($type == 'UPLOAD'){
                $type = 'POST';
                $data = $data;
            }
            else if($type == 'POST' and $data != null){
                $data = [
                    'form_params' => $data,
                ];
            }
            
            $headers_default = [
                'User-Agent'       => $this->user_agent,
                'Host'             => 'web.gediktrader.com',
                'X-Requested-With' => 'XMLHttpRequest',
                'Referer'          => 'https://web.gediktrader.com/v/',
            ];
            
            if(is_array($header)){
                foreach($header as $h_k => $h_v){
                    $headers_default[$h_k] = $h_v;
                }
            }
            
            if($user_cookie == true){
                $cookie            = $cookie??$this->create_cookie(false, $user_cookie);
                $headers_default['Cookie'] = $cookie;
            }
            
            try{
                $client = new \GuzzleHttp\Client([
                    'verify'  => false,
                    'headers' => $headers_default,
                ]);
                
                if($type == 'POST'){
                    $res = $client->post($url, $data);
                }
                else{
                    $res = $client->get($url);
                }
                
                return [
                    'status'  => 'ok',
                    'headers' => $res->getHeaders(),
                    'body'    => $res->getBody()->getContents(),
                ];
            }
            catch(GuzzleException $exception){
                return [
                    'status'  => 'fail',
                    'message' => $exception->getMessage(),
                    'headers' => $exception->getResponse()->getHeaders(),
                    'body'    => $exception->getResponse()->getBody()->getContents(),
                ];
            }
            
        }
        
        public function get_wss_id(){
            $wss_id = $this->cache('wss_id');
            if(is_array($wss_id)){
                return $wss_id[0];
            }
            return 0;
        }
        
        //KELİME BAŞLIYORSA
        function start_with($samanlik, $igne){
            $length = strlen($igne);
            return (substr($samanlik, 0, $length) === $igne);
        }
        //KELİME BAŞLIYORSA
        
        //KELİME BİTİYORSA
        function end_with($samanlik, $igne){
            $length = strlen($igne);
            if($length == 0){
                return true;
            }
            
            return (substr($samanlik, -$length) === $igne);
        }
        //KELİME BİTİYORSA
        
    }