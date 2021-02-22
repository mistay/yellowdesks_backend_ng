<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

use Cake\Event\Event;

class PaypalsController extends AppController {
    
    public function index() {
        if (!$this -> hasAccess([Roles::ADMIN])) return $this->redirect(["controller" => "users", "action" => "login", "redirect_url" =>  $_SERVER["REQUEST_URI"]]); 

        $model = TableRegistry::get('Paypalipns');
        $query = $model->find('all')->order("ts_inserted DESC");
        $this->set("rows", $query);
    }

    // user wird nach zahlung von paypal hierher umgeleitet:
    // https://www.yellowdesks.com/paypals/success/
    public function success() {
        // returning form paypal ends up with:
        // https://www.yellowdesks.com/paypals/success?amt=0.06&cc=EUR&cm=%5B1144%5D&item_name=Yellosdesks%20from%202017-04-13%20to%202017-04-13%20at%20host%20test&st=Completed&tx=3Y005173PM3737527
        
        // $_SERVER['HTTP_REFERER']
        // https://www.paypal.com/webapps/hermes?token=8E656568SH4720028&useraction=commit&rm=2&mfid=1492093548158_ef43ebc1210ea 

        // $_SERVER['REDIRECT_QUERY_STRING']
        // amt=0.06&cc=EUR&cm=%5B1142%5D&item_name=Yellosdesks%20from%202017-04-13%20to%202017-04-13%20at%20host%20test&st=Completed&tx=6NE32361EG7395357

        $model = TableRegistry::get('Paypalipns');
        $query = $model -> find('all') -> where (["txn_id" => $_REQUEST["tx"]]);
        $row = $query -> first();

        $json_booking_ids = $row -> custom;
        $booking_ids = json_decode($json_booking_ids);

        $user = $this -> getloggedinUser();

        // security: check if booking belongs to currently logged in user
        $model = TableRegistry::get('Bookings');
        $query = $model 
                    -> find('all') 
                    -> where (["Bookings.id IN" => $booking_ids])
                    -> contain(['Paypalipns', 'Hosts']);

        foreach ($query as $booking) {
            if ($booking -> coworker_id == $user -> id) {
                // booking really belongs to user, display confirmation

                $this->set("booking", $booking);
            }
            // todo: display other bookings as well but for now we do only support one booking per paypal ipn
        }
    }

    // this url is registered at paypal sandbox https://www.sandbox.paypal.com
    // https://www.yellowdesks.com/paypals/paypalipnsandbox
    public function paypalipnsandbox() {
        $this->paypalipn(true);
    }

    // this url is registered at paypal
    // https://www.yellowdesks.com/paypals/paypalipn
    public function paypalipn($sandbox = false) {
        $this->autoRender = false;
        $model = TableRegistry::get('Paypalipns');
        $row = $model -> newEntity();

        // this automatically sets fields like mc_gross, tx, .. as soon as they're setup in db table
        $model->patchEntity($row, $this -> request -> getData());

        $row -> rawrequest = json_encode($_REQUEST);
        $row -> sandbox = $sandbox;
        
        $ipn = new PaypalIPN();
        //$ipn->useSandbox(); // remove me for production
        $row -> verified = $ipn->verifyIPN();

        $row -> server = print_r($_SERVER, true);
        $model->save($row);
        $this->updatebookings($row->id);

        // Reply with an empty 200 response to indicate to paypal the IPN was received correctly.
        // header("HTTP/1.1 200 OK");
        // armin: this is done by cake per default
    }


    public function updatebookings($paypalipn_id) {
        $this -> autoRender = false;
        $ret = [];

        $model_paypalipns = TableRegistry::get('Paypalipns');
        $paypalipn = $model_paypalipns->get($paypalipn_id);
        
        // e.g. [1101]
        $custom = json_decode($paypalipn -> custom, true);

        $model2 = TableRegistry::get('Bookings');
        $rows = $model2->find('all')->where(['Bookings.id IN' => $custom]);
        $sum = 0;
        foreach ($rows as $booking) {
            $sum += $booking->price + $booking->vat;
        }
        
        $ret["sum_paypal"] = $paypalipn -> mc_gross;
        $ret["sum"] = $sum;
        $ret["diff"] = $paypalipn -> mc_gross - $sum;
        

        // compare floats, accept tolerance of 0.01 and of course more money than requested :)
        if ($paypalipn -> mc_gross - $sum > - 0.01) {
            $ret["sums_match"] = true;

            // überweisungsbetrag is i.O., alle bookings ueberwiesen, yehaa!
            foreach ($rows as $booking) {
                $booking -> paypalipn_id = $paypalipn_id;
                $model2 -> save($booking);
            }
        }

        if (strpos($_SERVER['REQUEST_URI'], "updatebookings") !== false) {
            $this->autoRender = false;
            $this->response->type('application/json');
            $this->response->body(json_encode($rets, JSON_PRETTY_PRINT));
        }
    }
}


//todo: errors sauber zu uns loggen
class PaypalIPN
{

    /**
     * @var bool $use_sandbox     Indicates if the sandbox endpoint is used.
     */
    private $use_sandbox = false;
    /**
     * @var bool $use_local_certs Indicates if the local certificates are used.
     */
    private $use_local_certs = true;

    /** Production Postback URL */
    const VERIFY_URI = 'https://ipnpb.paypal.com/cgi-bin/webscr';
    /** Sandbox Postback URL */
    const SANDBOX_VERIFY_URI = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';


    /** Response from PayPal indicating validation was successful */
    const VALID = 'VERIFIED';
    /** Response from PayPal indicating validation failed */
    const INVALID = 'INVALID';


    /**
     * Sets the IPN verification to sandbox mode (for use when testing,
     * should not be enabled in production).
     * @return void
     */
    public function useSandbox()
    {
        $this->use_sandbox = true;
    }

    /**
     * Sets curl to use php curl's built in certs (may be required in some
     * environments).
     * @return void
     */
    public function usePHPCerts()
    {
        $this->use_local_certs = false;
    }


    /**
     * Determine endpoint to post the verification data to.
     * @return string
     */
    public function getPaypalUri()
    {
        if ($this->use_sandbox) {
            return self::SANDBOX_VERIFY_URI;
        } else {
            return self::VERIFY_URI;
        }
    }


    /**
     * Verification Function
     * Sends the incoming post data back to PayPal using the cURL library.
     *
     * @return bool
     * @throws Exception
     */
    public function verifyIPN()
    {
        if ( ! count($_POST)) {
            //throw new Exception("Missing POST Data");
        }

        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                // Since we do not want the plus in the datetime string to be encoded to a space, we manually encode it.
                if ($keyval[0] === 'payment_date') {
                    if (substr_count($keyval[1], '+') === 1) {
                        $keyval[1] = str_replace('+', '%2B', $keyval[1]);
                    }
                }
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }

        // Build the body of the verification post request, adding the _notify-validate command.
        $req = 'cmd=_notify-validate';
        $get_magic_quotes_exists = false;
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        // Post the data back to PayPal, using curl. Throw exceptions if errors occur.
        $ch = curl_init($this->getPaypalUri());
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        // This is often required if the server is missing a global cert bundle, or is using an outdated one.
        if ($this->use_local_certs) {
            curl_setopt($ch, CURLOPT_CAINFO, APP . "/3rdparty/paypal/cacert.pem");
        }
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        $res = curl_exec($ch);
        if ( ! ($res)) {
            $errno = curl_errno($ch);
            $errstr = curl_error($ch);
            curl_close($ch);
            echo("cURL error: [$errno] $errstr");
        }

        $info = curl_getinfo($ch);
        $http_code = $info['http_code'];
        if ($http_code != 200) {
            //throw new Exception("PayPal responded with http code $http_code");
        }

        curl_close($ch);

        // Check if PayPal verifies the IPN data, and if so, return true.
        if ($res == self::VALID) {
            return true;
        } else {
            return false;
        }
    }
}
?>