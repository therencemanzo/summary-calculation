<?php
require_once('session.php');

define('NONCE_SECRET', 'ClaromentisPHPDeveloperTestTask');

class Nonce {
   
    public function generateNonce($form_id){
    
        $secret = NONCE_SECRET;

        if (is_string($secret) == false || strlen($secret) < 10) {
            throw new InvalidArgumentException("A valid Nonce Secret is required");
        }
       
        $salt = base64_encode(uniqid('', true));
      
        $time = time() + (60 * intval(10));
        $toHash = $secret.$salt.$time;

        $nonce = $form_id.':'.$time.':'.$salt.':'.hash('sha256', $toHash);
   
        $session = new Session();
        $session->storeSession(array('token' => md5($nonce)));
        unset($session);
   
        return $nonce;
    }
   public function verifyNonce($nonce){
  
        $secret = NONCE_SECRET;

        $split = explode(':', $nonce);
      
        $form_id = $split[0];
        $time = intval($split[1]);
        $salt = $split[2];
        $oldHash = $split[3];
    
        if(time() > $time){
            return false;
        }

        $session = new Session();
        $token = $session->getSession('token');
        unset($session);
        if($token !== null){
            //check if hashed value matches
            if($token !== md5($nonce)){
                return false;
            }
    
        }else{
             return false;
        }

        $toHash = $secret.$salt.$time;
        $reHashed = hash('sha256', $toHash);

        if($reHashed !== $oldHash){
            return false;
        }

        return true;
    }
}
?>