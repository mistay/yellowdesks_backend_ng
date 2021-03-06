<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Mailer\Email;
use Cake\Routing\Router;
use Cake\Core\Configure;

use Cake\Mailer\Mailer;
use Cake\Mailer\TransportFactory;

class UsersController extends AppController
{
    public function signupsuccess() {

    } 

    public function becomeahost() {
        $this->set("googlemapsapikey", Configure::read('googlemapsapikey'));


        if ($this -> request -> is('post')) {
            $data = $this -> getRequest() -> getData();

            // sticky form
            $this->set("data", $data);

            if (trim($data["name"]) == "") {
                $this -> Flash -> success (__("This is a B2B service only. Please provide your companyname."));
                return;
            }

            if (trim($data["firstname"]) == "") {
                $this -> Flash -> success (__("Please provide your first name."));
                return;
            }

            if (trim($data["lastname"]) == "") {
                $this -> Flash -> success (__("Please provide your first name."));
                return;
            }

            if (strpos($data["email"], "@") === false) {
                $this -> Flash -> success (__("Please provide your e-mail address."));
                return;
            }

            if (trim($data["lastname"]) == "") {
                $this -> Flash -> success (__("Please provide your address."));
                return;
            }

            if (trim($data["postal_code"]) == "") {
                $this -> Flash -> success (__("Please provide your postal code."));
                return;
            }

            if (trim($data["city"]) == "") {
                $this -> Flash -> success (__("Please provide your city."));
                return;
            }

            if (strlen($data["password"]) < 8) {
                $this -> Flash -> success (__("Please make sure your password is at least 8 characters long."));
                return;
            }

            if (!isset($data["termsandconditions"])) {
                $this -> Flash -> success (__("Please aggree to our terms and conditions."));
                return;
            }

            if ((int)$data["desks"] <= 0) {
                $this -> Flash -> success (__("Please provide at least one desk."));
                return;
            }

            if (trim($data["title"]) == "") {
                $this -> Flash -> success (__("Please provide a sloagen, a title for your yellow desks."));
                return;
            }

            if (trim($data["details"]) == "") {
                $this -> Flash -> success (__("Please explain what's included for your coworker."));
                return;
            }

            if (trim($data["extras"]) == "") {
                $this -> Flash -> success (__("Please explain what's excluded for your coworker."));
                return;
            }

            $model = TableRegistry::get('Hosts');
            $row = $model -> newEntity([]);

            // security: prevent tags like <script> to be inserted into db, so strip all tags
            $data["username"] = strip_tags($data["email"]);
            $data["nickname"] = strip_tags($data["firstname"]);
            $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);
            $data["name"] = strip_tags($data["name"]);
            $data["lastname"] = strip_tags($data["lastname"]);
            $data["email"] = strip_tags($data["email"]);
            $data["address"] = strip_tags($data["address"]);
            $data["postal_code"] = strip_tags($data["postal_code"]);
            $data["city"] = strip_tags($data["city"]);
            $data["desks"] = (int)($data["desks"]);
            $data["title"] = strip_tags($data["title"]);
            $data["details"] = strip_tags($data["details"]);
            $data["extras"] = strip_tags($data["extras"]);

            $data["lat"] = floatval($data["lat"]);
            $data["lng"] = floatval($data["lng"]);
            
            $model->patchEntity($row, $data);
            $model->save($row);

            $model->calclatlngloose();

            $message = __($this -> appconfigs ["welcomemailhosts"], 
                $data["firstname"], 
                Router::url(['controller' => 'Hosts','action' => 'cru'], true),
                Router::url(['controller' => 'Pictures','action' => ''], true),
                Router::url(['controller' => 'Videos','action' => ''], true),
                $this -> appconfigs ["emailfooter"]
                );

            $mailer = new Mailer();
            $mailer
                ->setTransport('lqh')
                ->setEmailFormat('html')
                ->setTo($data["email"])
                ->setFrom($this -> appconfigs["emailsender"])
                ->setSubject(__('Welcome to {0}', $this -> appconfigs['projectname']))
                ->viewBuilder()
                    ->setHelpers(['Html'])
                    ->setTemplate('default')
                    ->setLayout('fancy');

            $mailer->deliver("$message");

            $this->redirect(["action" => "signupsuccess"]);
        }
    }

    public function signup() {
        if ($this -> getRequest() -> is('post')) {
            $data = $this -> getRequest() -> getData();

            // sticky form
            $this->set("data", $data);

            if (trim($data["companyname"]) == "") {
                $this -> Flash -> success (__("This is a B2B service only. Please provide your companyname."));
                return;
            }

            if (trim($data["firstname"]) == "") {
                $this -> Flash -> success (__("Please provide your first name."));
                return;
            }

            if (trim($data["lastname"]) == "") {
                $this -> Flash -> success (__("Please provide your first name."));
                return;
            }

            if (strpos($data["email"], "@") === false) {
                $this -> Flash -> success (__("Please provide your e-mail address."));
                return;
            }

            if (strlen($data["password"]) < 8) {
                $this -> Flash -> success (__("Please make sure your password is at least 8 characters long."));
                return;
            }

            if (!isset($data["termsandconditions"])) {
                $this -> Flash -> success (__("Please aggree to our terms and conditions."));
                return;
            }
            
            if (!isset($data["spamprotect"]) || trim($data["spamprotect"]) != 'four') {
                $this -> Flash -> success (__("Please enter 'four' in spamprotect field"));
                return;
            }

            $model = TableRegistry::get('Coworkers');
            $row = $model -> newEntity([]);
            $data["username"] = $data["email"];
            $data["password"] = password_hash($data["password"], PASSWORD_BCRYPT);

            // security: prevent tags like <script> to be inserted into db, so strip all tags
            $data["companyname"] = strip_tags($data["companyname"]);
            $data["firstname"] = strip_tags($data["firstname"]);
            $data["lastname"] = strip_tags($data["lastname"]);
            $data["email"] = strip_tags($data["email"]);
            $model->patchEntity($row, $data);
            $model->save($row);



            $model->patchEntity($row, $data);
            $model->save($row);

            $url_coworker_profile =  Router::url(['controller' => 'Coworkers','action' => 'cru'], true);

            $message = __($this -> appconfigs ["welcomemailcoworkers"], 
                $row -> firstname, 
                $url_coworker_profile, 
                $this -> appconfigs ["emailfooter"]
                );

            $mailer = new Mailer();
            $mailer
                ->setTransport('lqh')
                ->setEmailFormat('html')
                ->setTo($row -> email)
                ->setFrom($this -> appconfigs["emailsender"])
                ->setSubject(__('Welcome to {0}', $this -> appconfigs['projectname']))
                ->viewBuilder()
                    ->setHelpers(['Html'])
                    ->setTemplate('default')
                    ->setLayout('fancy');

            $mailer->deliver($message);

            $this->redirect(["action" => "signupsuccess"]);
        }
    }

    public function resetpassword($email = "", $validatestring = "") {
        if (strlen($email) < 3) {
            $this -> Flash -> success (__("Please provide an E-Mail address with at least {0} characters.", 3));
            // todo: prevent controller from rendering view
            return;
        }
        if (strlen($validatestring) < 10) { // 10? how long are the hashes?
            // todo: prevent controller from rendering view
            $this -> Flash -> success (__("Please provide a validate string with at least {0} characters.", 10));
            return;
        }
        $user = $this -> searchUserByMail($email);
        
        if ($user -> count < 1) {
            $this -> Flash -> success (__("Sorry, no such E-Mail address found."));
            // todo: prevent controller from rendering view
            return;
        } elseif ($user -> count > 1) {
            $this -> Flash -> success (__("Sorry, more than one E-Mail address found. Please provide a unique E-Mail address."));
            // todo: prevent controller from rendering view
            return;
        } else {
            // genau eine e-mail adresse gefunden

            // user wants to set new password
            if (trim($user -> row -> passwordreset) != trim($validatestring)) {
                $this -> Flash -> success (__("Sorry, the provided validation string does not match any password reset request."));
                // todo: prevent controller from rendering view
                return;
            } else {
                if ($this -> request -> is('post')) {
                    $pass1 = $this->request->getData()["password1"];
                    $pass2 = $this->request->getData()["password2"];
                    
                    if ($pass1 == $pass2) {
                        $user -> row ["password"] = password_hash($pass1, PASSWORD_BCRYPT);
                        $user -> row ["passwordreset"] = null;
                        $user -> model -> save ($user -> row);
                        $this -> Flash -> success (__("Password updated successfully."));
                    } else {
                        $this -> Flash -> success (__("Passwords do not match."));
                    }
                }
            }
        }
    }

    private function searchUserByMail($email) {
        //$ret = stdClass();
        $modelCoworkers = TableRegistry::get('Coworkers');
        $modelHosts = TableRegistry::get('Hosts');
        
        $query = $modelCoworkers -> find('all') -> where(["Coworkers.email LIKE" => '%' . $email . '%']);
        $coworkers = $query->toArray();
        
        //todo: create std class object
        @$ret -> numCoworkers = sizeof($coworkers);

        $query = $modelHosts -> find('all') -> where(["Hosts.email LIKE" => '%' . $email . '%']);
        $hosts = $query->toArray();
        $ret -> numHosts = sizeof($hosts);

        $ret -> count = $ret -> numCoworkers + $ret -> numHosts;

        if ($ret -> numCoworkers + $ret -> numHosts == 1) {
            $ret -> model = $ret -> numCoworkers == 1 ? $modelCoworkers : $modelHosts;
            $ret -> row =  $ret -> numCoworkers == 1 ? $coworkers[0] : $hosts[0];
        }
        return $ret;
    }

    public function forgotpassword($email = "", $validatestring = "") {
        if ($this -> request -> is('post')) {
            $email = $this -> request -> data['email'];

            if (strlen($email) < 3) {
                $this -> Flash -> success (__("Please provide an E-Mail address with at least {0} characters.", 3));
                return;
            }
            $user = $this -> searchUserByMail($email);
            
            if ($user -> count < 1) {
                $this -> Flash -> success (__("Sorry, no such E-Mail address found."));
                return;
            } elseif ($user -> count > 1) {
                $this -> Flash -> success (__("Sorry, more than one E-Mail address found. Please provide a unique E-Mail address."));
                return;
            } else {
                // genau eine e-mail adresse gefunden

                $this -> Flash -> success (__("We sent you an email containing instructions for resetting your password. Please check your inbox."));
                $user -> row["passwordreset"] = base64_encode(rand());
                $user -> model -> save ($user -> row);
                $this -> sendRecoverInstructions($user -> row ["email"], $user -> row["passwordreset"]);
                
            }
        }
    }

    private function sendRecoverInstructions($to, $hash) {
        $reseturl =  Router::url([ 
            'controller' => 'Users','action' => 'resetpassword',
            $to,
            $hash,
            ], true);

        $message = __("Somebody requested to reset your email. If it was you, please navigate to {0} to set a new password.", $reseturl);

        $this -> sendMail($to, $this -> appconfigs["emailsender"], __('Reset your password on {0}', $this -> appconfigs['projectname'] ), $message);
    }
    
    public function loginappfb() {
        $ret = [];
        $ret["success"] = $this -> hasAccess([Roles::COWORKER]);
        if ( $ret["success"] ) {

            // todo: remove password field from result!!
            $loggedinuser = $this->getloggedInUser();
            $ret["loggedinuser"] = $loggedinuser;
        }
        $this->autoRender = false;
        $this->response->type('application/json');
        $this->response->body(json_encode($ret, JSON_PRETTY_PRINT));
    }
    
    function logout() {
        $this -> logoutSession();
        $this -> redirect('/');
    }
    
    public function home() {
        $model = TableRegistry::get('Hosts');
        $query = $model->find('all')
				->where(["enableyd" => 1])
				->contain(['Pictures'=> function ($q) {
                                                               return $q
                                                                    ->select(['id', 'host_id']);
                                                            },
                                               'Videos']);

        $this->set("rows", $query);

        $loggedinuser = $this -> getLoggedInUser();
        $this->set("loggedinuser", $loggedinuser);

        $this->set("googlemapsapikey", Configure::read('googlemapsapikey'));
    }

    public function welcome() {
        $loggedinuser = $this -> getLoggedInUser();

        if ($loggedinuser == null) {
            $this -> redirect(["controller" => "users", "action" => "login"]);
            return;
        }

        if ($loggedinuser -> role == Roles::ADMIN) {
            $this -> redirect(["controller" => "bookings", "action" => "index"]);
        } else if ($loggedinuser -> role == Roles::HOST) {
            $this -> redirect(["controller" => "bookings", "action" => "host"]);
        } else if ($loggedinuser -> role == Roles::COWORKER) {
            $this -> redirect(["controller" => "bookings", "action" => "mybookings"]);
        }
    }

    function getdetails() {
        $this->basicauth();
        $ret=[];
        $ret["error"] = "unknown error";
        
        $loggedinuser = $this -> getLoggedInUser();
        
        if ($loggedinuser == null) {
            $ret["error"] = "invalid username or password";
        } else {
            $ret["error"] = "";
            $ret["username"] = $loggedinuser->username;
            $ret["firstname"] = $loggedinuser->firstname;
            $ret["lastname"] = $loggedinuser->lastname;
        }
        
        $this->autoRender = false;
        $this->response->type('application/json');
        $this->response->body(json_encode($rets, JSON_PRETTY_PRINT));
    }
    
    function login() {
        $this->basicauth();
        if ($this -> getLoggedInUser() != null) {
            // user logged in
            
            //$target = $this -> initMenu(true);
            //$this -> redirect($target);
        }
        $success = null;
        
        if ($this->request->is('post')) {
            $username = $this -> getRequest() -> getData('username');
            $password = $this -> getRequest() -> getData('password');

            $this -> auth($username, $password);
            
            $this->basicauth();
            $user = $this -> getLoggedInUser();
            if ($user != null) {

                if ($user->role == ROLES::COWORKER)
                    $model = TableRegistry::get('Coworkers');
                if ($user->role == ROLES::HOST)
                    $model = TableRegistry::get('Hosts');
                if ($user->role == ROLES::ADMIN)
                    $model = TableRegistry::get('Admins');

                $row = $model -> get($user -> id);
                $row ["lastlogin"] = date("Y-m-d H:i:s"); // todo: this is local time
                $model->save($row);

                $this->redirect(["action" => "welcome"]);
            }
        }
        
        if ($this -> getLoggedInUser() != null) {
            if (isset($_REQUEST["redirect_url"])) {
                //echo "redirecting..." . $_REQUEST["redirect_url"] ... $this->redirect() does not redirect to absolute 
                // urls and thus /yellowdesks/yellowdesks/hosts/index url is generated instead of /yellowdesks/hosts/index

                $redirect_ttl = (int)$this -> getRequest() -> getSession() -> read ("redirec_ttl");
                $redirect_ttl++;

                $this -> getRequest() -> getSession() -> write ("redirec_ttl", $redirect_ttl);

                if ($redirect_ttl > 10) {
                    $this -> getRequest -> getSession() -> write ("redirec_ttl", 0);
                    $this -> Flash -> success (__("Too many redirects: Tried to redirect to {0} but ended up here.", $_REQUEST["redirect_url"]));
                } else  {
                    header("Location: " . $_REQUEST["redirect_url"]);
                    exit(0);
                }
            }
        }
    }
}
?>
