<?php

/**
 * Przelewy24 comunication class
 *
 * @author DialCom24 Sp. z o.o.
 * @copyright DialCom24 Sp. z o.o.
 * @version 1.1
 * @since 2014-04-29
 */

use bpmj\wpidea\integrations\Interface_External_Service_Integration;
use bpmj\wpidea\integrations\Trait_External_Service_Integration;

/**
 *
 * Communication protol version
 * @var double
 */
define("P24_VERSION", "3.2");

class Przelewy24 implements Interface_External_Service_Integration {
use Trait_External_Service_Integration;

    const SERVICE_NAME = 'Przelewy 24';
    /**
     * Live system URL address
     * @var string
     */
    private $hostLive        =    "https://secure.przelewy24.pl/";
    /**
     * Sandbox system URL address
     * @var string
     */
    private $hostSandbox     =    "https://sandbox.przelewy24.pl/";
    /**
     * Use Live (false) or Sandbox (true) enviroment
     * @var bool
     */
    private $testMode        =    false;
    /**
     * Merchant posId
     * @var int
     */
    private $posId           =    0;
    /**
     * Salt to create a control sum (from P24 panel)
     * @var string
     */
    private $salt            =    "";
    /**
     * Array of POST data
     * @var array
     */
    private $postData        =    array();

    /**
     *
     * Obcject constructor. Set initial parameters
     * @param int|null $merchantId
     * @param int|null $posId
     * @param string|null $salt
     * @param bool $testMode
     */
    public function __construct($merchantId = null, $posId = null, $salt = null, $testMode = false) {

        global $edd_options;

        $this->posId         =  isset($posId) ? (int)$posId : intval( $edd_options[ 'przelewy24_id' ] );
        $this->salt          = $salt ?? $edd_options[ 'przelewy24_pin' ];

        if($testMode) {
            $this->hostLive = $this->hostSandbox;
        }

        $this->addValue("p24_merchant_id", $merchantId ?? intval( $edd_options[ 'przelewy24_id' ] ));
        $this->addValue("p24_pos_id", $this->posId);
        $this->addValue("p24_api_version", P24_VERSION);

        return true;
    }
    /**
     *
     * Returns host URL
     */
    public function getHost() {
        return $this->hostLive;
    }

    /**
     *
     * Add value do post request
     * @param string $name Argument name
     * @param mixed $value Argument value
     * @todo Add postData validation
     */
    public function addValue($name, $value) {

        $this->postData[$name] = $value;

    }

    /**
     *
     * Function is testing a connection with P24 server
     * @return array Array(INT Error, Array Data), where data
     */
    public function testConnection() {

        $crc = md5($this->posId."|".$this->salt) ;

        $ARG["p24_pos_id"] = $this->posId;

        $ARG["p24_sign"] = $crc;

        $RES = $this->callUrl("testConnection",$ARG);
        return $RES;
    }

    /**
     *
     * Prepare a transaction request
     * @param bool $redirect Set true to redirect to Przelewy24 after transaction registration
     * @return array array(INT Error code, STRING Token)
     */
    public function trnRegister($redirect = false) {

        $crc = md5($this->postData["p24_session_id"]."|".$this->posId."|".$this->postData["p24_amount"]."|".$this->postData["p24_currency"]."|".$this->salt) ;

        $this->addValue("p24_sign", $crc);

        $RES = $this->callUrl("trnRegister",$this->postData);
        if($RES["error"] == "0") {

            $token = $RES["token"];

        } else {

            return $RES;

        }
        if($redirect) {
            $this->trnRequest($token);

        }

        return array("error"=>0, "token"=>$token);


    }

    /**
     * Redirects or returns URL to a P24 payment screen
     * @param string $token Token
     * @param bool $redirect If set to true redirects to P24 payment screen. If set to false function returns URL to redirect to P24 payment screen
     * @return string URL to P24 payment screen
     */
    public function trnRequest($token, $redirect = true) {

        if($redirect) {
            header("Location:" . $this->hostLive."trnRequest/".$token);
            return "";
        } else {
            return $this->hostLive."trnRequest/".$token;
        }

    }

    /**
     *
     * Function verify received from P24 system transaction's result.
     * @return array
     */
    public function trnVerify() {

        $crc = md5($this->postData["p24_session_id"]."|".$this->postData["p24_order_id"]."|".$this->postData["p24_amount"]."|".$this->postData["p24_currency"]."|".$this->salt) ;

        $this->addValue("p24_sign", $crc);

        $RES = $this->callUrl("trnVerify",$this->postData);

        return $RES;

    }

    /**
     *
     * Function contect to P24 system
     * @param string $function Method name
     * @param array $ARG POST parameters
     * @return array array(INT Error code, ARRAY Result)
     */
    private function callUrl($function, $ARG) {

        if(!in_array($function, array("trnRegister","trnRequest","trnVerify","testConnection"))) {

            return array("error"=>201,"errorMessage"=>"class:Method not exists");

        }

        $REQ = array();

        foreach($ARG as $k=>$v) $REQ[] = $k."=".urlencode($v);

        $url = $this->hostLive.$function;
        $user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
        if($ch = curl_init()) {

            if(count($REQ)) {
                curl_setopt($ch, CURLOPT_POST,1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,join("&",$REQ));
            }

            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            if($result = curl_exec ($ch)) {
                $INFO = curl_getinfo($ch);
                curl_close ($ch);

                if($INFO["http_code"]!=200) {

                    return array("error"=>200,"errorMessage"=>"call:Page load error (".$INFO["http_code"].")");

                } else {

                    $RES     = array();
     	            $X       = explode("&", $result);

     	            foreach($X as $val) {

                 		$Y           = explode("=", $val);
                 		$RES[trim($Y[0])] = urldecode(trim($Y[1]));
                 	}
             	    return $RES;

                }


            } else {
                curl_close ($ch);
                return array("error"=>203,"errorMessage"=>"call:Curl exec error");

            }

        } else {

            return array("error"=>202,"errorMessage"=>"call:Curl init error");

        }

    }

    public function check_connection(): bool
    {
        $response = $this->testConnection();
        if(isset($response["error"]) and $response["error"]==='0') {
            return true;
        }

        return false;
    }

}

?>
