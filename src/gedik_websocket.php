<?php
    
    
    namespace gedik_yatirim;

    class gedik_websocket extends gedik_request{
        
        public $wss_url = 'wss://web.gediktrader.com/v/client/connector?X-Atmosphere-tracking-id=0&X-Atmosphere-Framework=2.2.11-javascript&X-Atmosphere-Transport=websocket&X-Atmosphere-TrackMessageSize=true&Content-Type=text/x-gwt-rpc;%20charset=UTF-8&X-atmo-protocol=true&sessionState=dx-new';
        public $username;
        public $password;
        public $functions;
        
        public $output = null;
        
        public $telegram_token = '1126764049:AAEwnURzjlxFgjrqdi-F5RcYN6ZaSOmn0Vw';
        
        function __construct($username, $password, $functions = null){
            $this->username  = $username;
            $this->password  = $password;
            $this->functions = $functions;
        }
        
        public function ws_login($session_id = null){
            if($session_id != null){
                $this->wss_connect($session_id, 41, true);
            }
            return false;
        }
        
        public function wss_connect($session_id = null, $control = 41, $auto_close = true){
            if($session_id != null){
                \Ratchet\Client\connect($this->wss_url, [], [
                    'Origin' => 'https://web.gediktrader.com',
                    'Cookie' => 'JSESSIONID='.$session_id.';',
                ])->then(function($conn) use ($control, $auto_close){
                    $conn->on('message', function($msg) use ($conn, $control, $auto_close){
                        [$this, 'control_'.$control]($msg, $conn);
                        $conn->close();
                    });
                }, function($e){
                    echo "Could not connect: {$e->getMessage()}\n";
                });
            }
        }
        
        private function control_41($message = null, $conn = null){
            if($conn != null){
                if(!empty($message) and $this->start_with($message, '41')){
                    preg_match('|41\|(.*?)\|(.*?)|is', $message, $id);
                    $this->cache_time = 0;
                    $this->cache('wss_id', [$id[1]]);
                    $this->set_output([$id[1]]);
                    $conn->close();
                    return [$id[1]];
                }
            }
            return false;
        }
    
        public function set_output($message = null){
            echo 'set';
            return $this->output = $message;
        }
        
        public function get_output(){
            echo 'get';
            return $this->output;
        }
    }
    
    $wss = new gedik_websocket();