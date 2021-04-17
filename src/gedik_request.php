<?php
    
    namespace gedik_yatirim;
    
    use GuzzleHttp\Exception\GuzzleException;
    
    class gedik_request{
        
        public $headers;
        
        public $cache_path   = (__DIR__).'/cache/';
        public $cache_prefix = 'gedik';
        public $cache_time   = 10; //Minute
        
        public $user_agent = 'Instagram 177.0.0.30.119 Android (22/5.1.1; 160dpi; 540x960; Google/google; google Pixel 2; x86; qcom; tr_TR; 276028050)';
        
        public $username;
        public $password;
        
        function __construct($username, $password){
            $this->username = $username;
            $this->password = $password;
        }
        
        public function create_cookie($array = false, $session_id = true){
            
            $cookies_array = [
                'mid'       => 'YB2r4AABAAERcl5ESNxLjr_tt4Q5',
            ];
            
            if($session_id === true){
                $cookies_array['sessionid'] = '';
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
        
        public function cache($name, $desc = false, $json = false){
            
            if(!file_exists($this->cache_path.$this->username)){
                mkdir($this->cache_path.$this->username, 777);
            }
            
            $cache_file_path = $this->cache_path.$this->username.'/';
            $cache_file      = $cache_file_path.($name.'.json');
            
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
                'User-Agent' => $this->user_agent,
            ];
            
            $headers = $header??$headers_default;
            
            if($user_cookie == true){
                $cookie            = $cookie??$this->create_cookie(false, $user_cookie);
                $headers['Cookie'] = $cookie;
            }
            
            try{
                $client = new \GuzzleHttp\Client([
                    'verify'  => false,
                    'headers' => $headers,
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