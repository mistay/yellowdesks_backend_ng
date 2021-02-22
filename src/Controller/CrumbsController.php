<?php
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;


use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Network\Session\DatabaseSession;
use Cake\ORM\TableRegistry;

use Cake\Routing\Router;

use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Mailer\Email;

/* v1.46
 * first edit: 20130628
 * last changed: 20140704
 *
 * new features:
 *      - new function: getMonthsBetweenDates
 *      - added setheaderscsv();
 *      - added backup
 *      - added freshInstall()
 * v1.06 - filter _LIKE hinzugef√ºgt, sodass man fix angeben kann, dass man zB auch mit nummern (barcodes) auch mit LIKE f kann
 * v1.15 - '<br />' eingef√ºgt, sodass Backupinfo nicht direkt am letzten Men√ºpunkt h√§ngt
 * v1.24 - Browserpr√ºfung validateBrowser() beim Login hinzugef√ºgt
 * v1.25 - Email-Versand hinzugef√ºgt sendMail()
 *
 * fixed bugs:
 *       - $filename wurde in setheaderscsv(); erg√§nzt
 * v1.07  - enforceUser stellt erst NACH login sicher, dass der benutzer sein passwort setzen muss
 * v1.08  - initMenu() alle meunpunkte der 2. ebene sind per default ausgeklappt
 * v1.09  - initVersion() aus crumscontroller herausgezogen und in appcontroler √§bertragen
 * v1.10 - initMenu() is_enabled bedinung ausgewertet
 * v1.11 - "Backup" Ab√§ngigkeit hinzugef√ºgt, weil Backup als Standardfunktionalit√§t gesehen werden sollte und keine projektspezifische Sache sein sollte.
 * v1.12 - fixed doku
 * v1.13 - fixed menu: es war nur 1 submenupunkt moeglich, ab jetzt beliebig viele
 * v1.14 - fixed menu: rollensystem auf keys umgestellt und keys mit :admin:bla:foo konfigurierbar
 * v1.15 - improved backup filesize. only displayed if available
 * v1.16
 * v1.17 - added class EasyTransaction
 * v1.18 - added setFlash in EasyTransaction
 * v1.19 - EasyTransaction: maschinenlesbare weiterverarbeitung
 * v1.20 - added qr()
 * v1.21 - merged bct crums and ti crumbs
 * v1.22 - htmlentities im menu (name)
 * v1.23 - improved EasyTransaction (introduced RedirectMethod)
 * v1.26 - removed pg_escape_string in sendMail()
 *         introduced CrumbsController::redirectRequested (bool) as redirect-lock for enforceUserPwd(), freshInstall() and forceCustomerSelection(). otherwise you will be redirected circularly
 * v1.27 - bugfix with redirectRequested; changed to $beforeRenderRedirect
 * v1.28 - beforeRenderHook eingef√ºhrt
 * v1.29 - lockAnyFurtherRedirect eingef√ºhrt
 * v1.30 - bcc bei sendMail hinzugef√ºgt
 * v1.31 - fixed m√§rz utf8 encoding in $i18nMonths
 * v1.32 - fixed sendmail footer
 * v1.33 - added support for initMenu() to display menu even when the user is not logged in
 * v1.34 - added menu icon support
 * v1.35 - added auth() and setpassng()
 * v1.36 - added printPrinter()
 * v1.37 - added $printer argument in signature of printPrinter()
 * v1.38 - added gpsdistance()
 * v1.39 - fixed/added support for non-backup sites as interreg, see initBackup()
 * v1.40 - added getdistancesqlfield(lat, lng, lat2, lng2)
 * v1.41 - fixed path for phpqrcode.php and in printPrinter()
 * v1.42 - added printZpl()
 * v1.43 - added initLoggedInAsNG()
 * v1.44 - added 1st parameter for initMenu(): $onlyFirstLink
 * v1.45 - removed setting "active2" class to menu elements: this is now done by js: menu.js in order to apply with bctconfigweb
 * v1.46 - added initMenu() actioncounter
 *  * * */


class Roles {
    const ADMIN = "ADMIN";
    const COWORKER = "COWORKER";
    const HOST = "HOST";
    
    
}
/** use:
 *
 * $transaction = new EasyTransaction($this);
 * ...
 * $transaction->setSuccess(); // im erfolgsfall
 *
 * weiter unten ...
 *
 * $transaction->close(); .. redirected im erfolgs und meldet fehler im fehlerfall
 *
 */
class EasyTransaction {

    const REDIRECTMETHODFLASH = "REDIRECTMETHODFLASH";
    const REDIRECTMETHODREDIRECT = "REDIRECTMETHODREDIRECT";
    const REDIRECTMETHODSETFLASH = "REDIRECTMETHODSETFLASH";
    const REDIRECTMETHODECHO = "REDIRECTMETHODECHO";
    const REDIRECTMETHODDONOTHING = "REDIRECTMETHODDONOTHING";

    var $redirectMethod = EasyTransaction::REDIRECTMETHODFLASH;
    var $redirect = "javascript:history.back()";
    var $error = "unknown error";
    var $datasource = "";
    private $controller;
    private $model;

    function __construct($controller) {

        $this -> controller = $controller;

        $model = ClassRegistry::init('Config');
        $this -> model = $model;
        //print_r($model);
        $this -> datasource = $model -> getDataSource();
        $this -> datasource -> begin();
    }

    function setSuccess() {
        $this -> error = false;
    }

    function isSuccessful() {
        return $this -> error === false;
    }

    function close() {

        if ($this -> isSuccessful()) {
            $this -> datasource -> commit();
            //echo "close" . $this->isSuccessful(); exit(0);
        } else {
            $this -> datasource -> rollback();
        }
        switch ($this->redirectMethod) {
            case EasyTransaction::REDIRECTMETHODFLASH :
                $this -> controller -> flash($this -> error, $this -> redirect);
                break;
            case EasyTransaction::REDIRECTMETHODREDIRECT :
                $this -> controller -> redirect($this -> redirect);
                break;
            case EasyTransaction::REDIRECTMETHODSETFLASH :
                $this -> model -> setFlash($this -> error);
                break;
            case EasyTransaction::REDIRECTMETHODECHO :
                // f‚àö¬∫r seiten die zB maschinenlesbar verarbeitet werden
                echo($this -> error);
                break;
        }
    }

}

class Formchecks {

    static function notempty($foo) {
        return empty($foo);
    }

    static function numeric($foo) {
        return is_numeric($foo);
    }

}

class Filter {

    const init = "init";
    const filternamespace = "filternamespace";
    const PREFIX = "filter_";

}

class Validate {

    const number = "number";
    const string = "string";
    const date = "date";
    const isempty = "isempty";

}

class TimeDate {

    static $i18nWeekdays = array("de" => array(1 => "Montag", 2 => "Dienstag", 3 => "Mittwoch", 4 => "Donnerstag", 5 => "Freitag", 6 => "Samstag", 7 => "Sonntag"));
    static $i18nMonths = array("de" => array(1 => "Januar", 2 => "Februrar", 3 => "M√§rz", 4 => "April", 5 => "Mai", 6 => "Juni", 7 => "Juli", 8 => "August", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Dezember", ));

}

//http://crackstation.net/hashing-security.htm#phpsourcecode
define("PBKDF2_HASH_ALGORITHM", "sha256");
define("PBKDF2_ITERATIONS", 1000);
define("PBKDF2_SALT_BYTES", 24);
define("PBKDF2_HASH_BYTES", 24);

define("HASH_SECTIONS", 4);
define("HASH_ALGORITHM_INDEX", 0);
define("HASH_ITERATION_INDEX", 1);
define("HASH_SALT_INDEX", 2);
define("HASH_PBKDF2_INDEX", 3);

class SaltedHash {
    /*
     * Password hashing with PBKDF2.
     * Author: havoc AT defuse.ca
     * www: https://defuse.ca/php-pbkdf2.htm
     */

    // These constants may be changed without breaking existing hashes.

    static function create_hash($password) {
        // format: algorithm:iterations:salt:hash
        $salt = base64_encode(mcrypt_create_iv(PBKDF2_SALT_BYTES, MCRYPT_DEV_URANDOM));
        return PBKDF2_HASH_ALGORITHM . ":" . PBKDF2_ITERATIONS . ":" . $salt . ":" . base64_encode(self::pbkdf2(PBKDF2_HASH_ALGORITHM, $password, $salt, PBKDF2_ITERATIONS, PBKDF2_HASH_BYTES, true));
    }

    static function validate_password($password, $good_hash) {
        $params = explode(":", $good_hash);
        if (count($params) < HASH_SECTIONS)
            return false;
        $pbkdf2 = base64_decode($params[HASH_PBKDF2_INDEX]);
        return self::slow_equals($pbkdf2, self::pbkdf2($params[HASH_ALGORITHM_INDEX], $password, $params[HASH_SALT_INDEX], (int)$params[HASH_ITERATION_INDEX], strlen($pbkdf2), true));
    }

    // Compares two strings $a and $b in length-constant time.
    static function slow_equals($a, $b) {
        $diff = strlen($a) ^ strlen($b);
        for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
            $diff |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $diff === 0;
    }

    /*
     * PBKDF2 key derivation function as defined by RSA's PKCS #5: https://www.ietf.org/rfc/rfc2898.txt
     * $algorithm - The hash algorithm to use. Recommended: SHA256
     * $password - The password.
     * $salt - A salt that is unique to the password.
     * $count - Iteration count. Higher is better, but slower. Recommended: At least 1000.
     * $key_length - The length of the derived key in bytes.
     * $raw_output - If true, the key is returned in raw binary format. Hex encoded otherwise.
     * Returns: A $key_length-byte key derived from the password and salt.
     *
     * Test vectors can be found here: https://www.ietf.org/rfc/rfc6070.txt
     *
     * This implementation of PBKDF2 was originally created by https://defuse.ca
     * With improvements by http://www.variations-of-shadow.com
     */

    static function pbkdf2($algorithm, $password, $salt, $count, $key_length, $raw_output = false) {
        $algorithm = strtolower($algorithm);
        if (!in_array($algorithm, hash_algos(), true))
            die('PBKDF2 ERROR: Invalid hash algorithm.');
        if ($count <= 0 || $key_length <= 0)
            die('PBKDF2 ERROR: Invalid parameters.');

        $hash_length = strlen(hash($algorithm, "", true));
        $block_count = ceil($key_length / $hash_length);

        $output = "";
        for ($i = 1; $i <= $block_count; $i++) {
            // $i encoded as 4 bytes, big endian.
            $last = $salt . pack("N", $i);
            // first iteration
            $last = $xorsum = hash_hmac($algorithm, $last, $password, true);
            // perform the other $count - 1 iterations
            for ($j = 1; $j < $count; $j++) {
                $xorsum^=($last = hash_hmac($algorithm, $last, $password, true));
            }
            $output .= $xorsum;
        }

        if ($raw_output)
            return substr($output, 0, $key_length);
        else
            return bin2hex(substr($output, 0, $key_length));
    }

}



class CrumbsController extends Controller {

    var $uses = array("User", "Config", "Backup", "Menuposition", "Role", "Upload", "Customer", "Contingent", "Assignment", "Assignmenttype", "Maillog");

    private $redirectRequested = false;

    private $beforeRenderRedirectLock = false;
    private $beforeRenderRedirect = "";
    function setBeforeRenderRedirectURL($url) {
        if ($this -> beforeRenderRedirect == "" && $this -> beforeRenderRedirectLock == false)
            $this -> beforeRenderRedirect = $url;

    }

    function lockAnyFurtherRedirect() {
        $this -> beforeRenderRedirectLock = true;
    }

    // kann in AppController mit richtiger Versionsnummer ÔøΩberschrieben werden
    function initVersion() {
        $this -> set("version", "<div class='backup version'>V0.1</div>");
    }

    // kann in AppController √ºberschrieben werden
    function beforeRenderHook() {

    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow users to register and logout.
        // You should not add the "login" action to allow list. Doing so would
        // cause problems with normal functioning of AuthComponent.
        //$this->Auth->allow(['add', 'logout', 'home', 'loginappfb']);
        
        
    //}
    
    
   // public function beforeRender(Event $event) {
        setlocale(LC_MONETARY, 'de_DE');

        session_start();
        
        //echo "session started";
        
        $this -> initLoggedInAsNG();
        //$this -> initMenu();
        //$this -> initBackup();
        //$this -> initVersion();
        //$this -> initBreadcrumbs();

        //$this -> freshInstall();
        //$this -> enforceUserSettingPassword();

        //$this -> beforeRenderHook();

        //if ($this -> beforeRenderRedirect != "")
        //    $this -> redirect($this -> beforeRenderRedirect);

    }

    function qr($text, $size = 3) {
        require_once (APP . "/3rdparty/phpqrcode/phpqrcode.php");

        ob_start();
        QRcode::png($text, false, QR_ECLEVEL_H, $size, 0);
        $png = ob_get_contents();
        ob_end_clean();
        return $png;
    }

    function recentbackup($is_local) {

        $backup = $this -> Backup -> find("first", array("conditions" => array("is_local" => $is_local, "succeeded" => true), "order" => array("uts_inserted DESC")));
        return $backup;
    }

    function initBackup() {
        if (!$this -> Backup -> useTable)
            return;
        $ret = '<br />';
        $ret = '<div class="backup">';

        $ret .= '<div class="localtime">local time: ' . date("d.m.Y H:i:s") . '</div>';

        $tmp = $this -> recentbackup(1);
        $class = "backuperror";
        $tmp_date = "none";
        if (isset($tmp["Backup"]["uts_inserted"])) {
            $tmp_date = date("d.m. H:i:s", $tmp["Backup"]["uts_inserted"]);
            if (time() - $tmp["Backup"]["uts_inserted"] < 1 * 24 * 60 * 60)
                $class = "";
            // backup j√ºnger als 1 tag, das is ok
        }
        $ret .= '<div class="localbackup ' . $class . '">local bak: ' . $tmp_date . " (" . $this -> formatSizeUnits($tmp["Backup"]["size"]) . ')</div>';

        $tmp = $this -> recentbackup(0);
        $class = "backuperror";
        $tmp_date = "none";
        if (isset($tmp["Backup"]["uts_inserted"])) {
            $tmp_date = date("d.m. H:i:s", $tmp["Backup"]["uts_inserted"]);
            if (time() - $tmp["Backup"]["uts_inserted"] < 1 * 24 * 60 * 60)
                $class = "";
            // backup j√ºnger als 1 tag, das is ok
        }
        $ret .= '<div class="remotebackup ' . $class . '">remote bak: ' . $tmp_date . " (" . $this -> formatSizeUnits($tmp["Backup"]["size"]) . ')</div>';

        $ret .= "</div>";

        $this -> set("backup", $ret);
    }

    private function formatSizeUnits($bytes) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . 'GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . 'MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . 'kB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . 'b';
        } elseif ($bytes == 1) {
            $bytes = $bytes . 'b';
        } else {
            $bytes = '0b';
        }

        return $bytes;
    }

    private function freshInstall() {
        $projectName = $this -> Config -> getConfigValue("projectName");
        //echo "name: " . $projectName;
        if ($projectName == "") {
            // wir nehmen an, dass es sich um eine frische installation handelt.
            // ein benutzer muss angelegt werden und die datenbank muss mit den defaults befuellt werden
            if (!strstr($_SERVER["REQUEST_URI"], "/users/freshinstall")) {
                $this -> setBeforeRenderRedirectURL("/users/freshinstall");
            } else {
                $this -> lockAnyFurtherRedirect();
            }
        }
    }

    function setheaderscsv($filename = "download.csv") {

        $this -> response -> type('csv');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-Disposition: attachment;filename=$filename");
        header("Content-Transfer-Encoding: binary");
    }

    function smartvalidate($type, $data) {
        if ($type == Validate::number) {
            if (is_numeric($data))
                return "";
            else
                return "nonnumeric";
        }

        if ($type == Validate::date) {
            if (strtotime($data) > 0)
                return "";
            else
                return "nondate";
        }

        return false;
    }

    function getLoggedInUser() {
        $user = $this->getRequest()->getSession()->read('User');
        return $user;
    }

    function getLoggedInRole() {
        $user = ($this -> getRequest() -> getSession() -> read("User"));
        return $user -> role;
    }

    function initBreadcrumbs() {
        $breadcrumb_array = split("/", $_SERVER["REQUEST_URI"]);

        //print_r($breadcrumb_array);

        $retarray = array();
        array_push($retarray, array($this -> Config -> getConfigValue("projectName"), "/"));
        if (isset($breadcrumb_array[1])) {
            array_push($retarray, array($breadcrumb_array[1], "/" . $breadcrumb_array[1]));
        }

        if (isset($breadcrumb_array[2])) {
            array_push($retarray, array($breadcrumb_array[2], "/" . $breadcrumb_array[2]));
        }

        $ret = "";
        $ret .= "<ul>";
        for ($i = 0; $i < sizeof($retarray); $i++) {
            if ($i < sizeof($retarray) - 1) {
                $ret .= "<li><a href='" . $retarray[$i][1] . "'>" . $retarray[$i][0] . "</a></li>";
            } else {
                // letztes element in liste nicht verlinken
                $ret .= "<li>" . $retarray[$i][0] . "</li>";
            }
        }
        $ret .= "</ul>";
        $this -> set("breadcrumbs", $ret);
    }

    function initLoggedInAsNG() {
        $ret = array();

        $loggedInUser = $this -> getLoggedInUser();
        if ($loggedInUser == null) {
            $tmp = __("not logged in.");
            $tmp .= "<a href='" . Router::url(['controller' => 'users', 'action' => 'login']) . "'>".__("login")."</a>";
        } else {
            $tmp = __("logged in as {0} with username {1}. ", $loggedInUser -> role, $loggedInUser -> username);
            $tmp .= "<a href='" . Router::url(['controller' => 'users', 'action' => 'logout']) . "'>".__("logout")."</a>";
             
        }
        
        $this->set("loggedInAs", $tmp);
        $this->set ("loggedInUser", $loggedInUser);
        
    }

    // deprecated
    function initLoggedInAs() {

        $loggedInAs = "<div class='login_txt'>";
        if (($loggedInUserID = $this -> getLoggedInUserID()) > 0) {
            $userDetails = $this -> User -> find("first", array("conditions" => "User.id=" . $loggedInUserID));

            $username = $userDetails['User']['username'];
            $role = $userDetails['Role']['name'];
            $loggedInAs .= "<span>Hallo </span>" . $username . ", " . $role . "";

            $loginlogoutbutton = '<a href="/users/logout" class="logout">LOGOUT</a>';
        } else {
            $loggedInAs .= "";

            $loginlogoutbutton = '<a href="/users/login" class="logout">LOGIN</a>';
        }

        $loggedInAs .= "</div>";
        //echo $loggedInAs;
        $this -> set(compact("loggedInAs"));
        $this -> set(compact("loginlogoutbutton"));
    }

    function getRoleIdByKey($key) {
        $role = $this -> Role -> find("first", array("conditions" => array("Role.key" => $key, "Role.is_enabled" => 1)));
        return $role["Role"]["id"];
    }

    function basicauth() {
        if (isset($_SERVER['HTTP_LOGINTARGET']) && $_SERVER['HTTP_LOGINTARGET'] == "FACEBOOK") {
            if (isset($_SERVER["PHP_AUTH_PW"])) {
                $this->authfacebook($_SERVER["PHP_AUTH_PW"]);
            }
        } else {
            if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
                $this->auth($_SERVER["PHP_AUTH_USER"], $_SERVER["PHP_AUTH_PW"]);
            }
        }
    }
    function hasAccess($requiredRoles = array()) {
        $this -> basicauth();
        $user = $this -> getLoggedInUser();
        
        if ($user != null) {

            // user ist angemeldet
            if (is_array($requiredRoles)) {
                foreach ($requiredRoles as $requiredRole) {
                    
                    if ($user -> role == $requiredRole) {
                        // user besitzt eine der geforderten rollenrechte
                        return true;
                    } else {
                        // user hat nicht die richtigen rollenrechte
                    }
                }
            } elseif (isset($requiredRoles)) {
                // nicht mehrere rollen sondern nur eine rolle angegeben
                // zB     if ($this->requireAccess(false,       "admin" )) {
                // statt  if ($this->requireAccess(false, array("admin"))) {
                if ($user -> role == $requiredRoles) {
                    // user hat eine der geforderten rollenrechte
                    return true;
                }
            } else {
                //echo "user braucht keine rollenberechtigung";
                // keine rollenrechte nötig, alle angemeldeten user dürfen das sehen
                return true;
            }
        } else {
            // user nicht angemedet
            return false;
        }
        

        // default, keine berechtigung
        return false;
    }

    function enforceUserSettingPassword() {

        //phpinfo();
        $user = $this -> User -> find("first", array("fields" => array("User.is_pass_init"), "conditions" => array("User.id" => $this -> getLoggedInUserID(), )));

        if (sizeof($user) > 0) {
            if ($user["User"]["is_pass_init"] == true) {
                if (!strstr($_SERVER["REQUEST_URI"], "/users/setpass")) {
                    $this -> setBeforeRenderRedirectURL("/users/setpass");
                } else {
                    $this -> lockAnyFurtherRedirect();
                }
            }
        }
    }

    function initMenu($onlyFirstLink = false) {

        //echo $_SERVER["REQUEST_URI"];
        $menu = "<ul>";

        //$query = "select * from menupositions where parentID=0 and requiredRoleIDs like '%" . $this->getLoggedInRoleID() . "%' and isEnabled='y' " . " order by sequence";

        $rolekey = "notloggedin";
        if ($this -> getLoggedInRoleID() > 0) {
            $user = $this -> User -> find("first", array("recursive" => 0, "fields" => "Role.key", "conditions" => array("User.id" => $this -> getLoggedInUserID())));
            $rolekey = $user["Role"]["key"];
        }
        $mainmenuentries = $this -> Menuposition -> find("all", array("order" => "sequence ASC", "conditions" => array("requiredrolekeys LIKE" => "%:" . $rolekey . ":%", "parent_id" => 0, "is_enabled" => true)));
        $submenuentries = $this -> Menuposition -> find("all", array("order" => "sequence ASC", "conditions" => array("requiredrolekeys LIKE" => "%:" . $rolekey . ":%", "parent_id !=" => 0, "is_enabled" => true)));

        //print_r($mainmenuentries);
        $sm = array();
        foreach ($submenuentries as $submenuentry) {
            if (!isset($sm[$submenuentry['Menuposition']["parent_id"]])) {
                $sm[$submenuentry['Menuposition']["parent_id"]] = array();
            }
            $sm[$submenuentry['Menuposition']["parent_id"]][] = $submenuentry;
        }

        $classes = array();
        //print_r($sm);
        //print_r($mainmenuentries);
        //print_r($submenuentries);
        $menu .= "<li class='rebootneeded'><a href=''>reboot needed<span class='actioncounter'>!</span></a></li>";

        foreach ($mainmenuentries as $mainmenuentry) {
            $link = trim($mainmenuentry['Menuposition']['link']);

            // caller möchte nur ersten menueintrag wissen, insb. /Users/login
            if ($onlyFirstLink && $link != "")
                return $link;

            $active = "";
            if (@strstr($_SERVER["REQUEST_URI"], $link)) {
                $active = 'class="active"';
            }

            $menu .= "<li " . ($mainmenuentry['Menuposition']['icon'] == null ? "" : "class='" . $mainmenuentry['Menuposition']['icon'] . "'") . ">";
            $menu .= $link == "" ? "" : "<a $active href='" . $link . "'>";
            $menu .= htmlentities($mainmenuentry['Menuposition']['name'], null, "UTF-8");
            $menu .= $mainmenuentry["Menuposition"]['actioncounter'] == "" ? "" : "<span class='actioncounter'>" . $mainmenuentry["Menuposition"]['actioncounter'] . "</span>";
            $menu .= $link == "" ? "" : "</a>\n";

            // ************* second level menu ****************
            //echo "second level for: " . $mainmenuentry['Menuposition']["id"] . "? " . sizeof(@$sm[$mainmenuentry['Menuposition']["id"]]) . "\n";
            //if ($active != "") {
            if (sizeof(@$sm[$mainmenuentry['Menuposition']["id"]]) > 0) {
                $menu .= "<ul class='nav2'>";
                foreach ($sm[$mainmenuentry['Menuposition']["id"]] as $key => $submenuentry) {
                    //print_r($submenuentry);

                    //now done in jscript//$active2 = "";
                    //now done in jscript//if (strstr($_SERVER["REQUEST_URI"], $submenuentry["Menuposition"]['link']) !== false) {
                    //now done in jscript//$active2 = 'class="active2"';
                    //now done in jscript//}
                    $menu .= "<li>\n";
                    $menu .= $submenuentry["Menuposition"]['link'] == "" ? "" : "<a href='" . $submenuentry["Menuposition"]['link'] . "'>";
                    $menu .= htmlentities($submenuentry["Menuposition"]['name'], null, "UTF-8");
                    $menu .= $submenuentry["Menuposition"]['actioncounter'] == "" ? "" : "<span class='actioncounter'>" . $submenuentry["Menuposition"]['actioncounter'] . "</span>";
                    $menu .= $submenuentry["Menuposition"]['link'] == "" ? "" : "</a>";
                    $menu .= "</li>\n";
                }
                $menu .= "</ul>\n";
            }
            //}
            //echo "nachif";
            // ************* second level menu ****************

            $menu .= "</li>";
        }

        $menu .= "</ul>";
        $this -> set("menu", $menu);
    }

    /**
     * initFilterVariable()
     * e.g.$init = array(
     "filter_Assignments_Assignment#uts>_uts_assignmentstart" => "1.2.2013",
     "filter_Assignments_Assignment#uts<_uts_assignmentend" => "1.2.2014",
     "filter_Assignments_Assignment#assignmentstate_ids" => array($this->getAssignmentStateIdByKey(Assignmentstates::approved), $this->getAssignmentStateIdByKey(Assignmentstates::finished)),
     );
     *
     *
     */
    private function initFilterVariable($variables = array()) {
        foreach ($variables as $variable => $value) {
            $oldvalue = $this -> Session -> read($variable);
            //echo "old:" . $oldvalue . ".";
            if (!isset($oldvalue)) {
                $this -> Session -> write($variable, $value);
            }
        }
    }

    function flushSessionApp($silent = false) {

        ($this -> Session -> read("foo"));
        // ohne dem gehts komischerweise nicht ...? da ist dann $_session leer

        if (!$silent)
            echo "vorher<br />";
        if (!$silent)
            print_r($_SESSION);

        if (!$silent)
            print_r($_SESSION);
        foreach ($_SESSION as $key => $value) {
            //if (strpos($key, "filter_") === 0) {
            if (!$silent)
                echo "deleting: " . $key . "<br />";
            $this -> Session -> delete($key);
            //}
        }

        foreach ($_SESSION as $session) {
            $this -> Session -> delete($session);
        }

        if (!$silent)
            echo "<br /><br />nachher<br />";
        if (!$silent)
            print_r($_SESSION);

        $this -> autoRender = false;
    }

    /**
     *
     * @param type $args
     * ["init"]: array mit zu initialisierenden Filtervariablen, zB
     * $args["initFilterVariables"] = array("filter_Holding#id" => 3);
     *
     * ["namespace"]: string, der die session variable in einen namespace legt, der angewendet wird
     * $args["namespace"] = "chains";
     *
     * Beispiele:
     * ==========
     *
     * Datumsfeld:
     * -----------
     * filter_Assignment#uts>_uts_assignmentstart
     * filter_Assignment#uts<_uts_assignmentstart
     *
     * is-Enabled feld:
     * ----------------
     * <input type="checkbox" name="filter_Assignment#is_enabled" value="0" onclick="document.form1.submit()"
     * <?php if (@$filter["filter_Assignment#is_enabled"] === "0") echo "checked='checked'";

     ?>
     />
     *
     * ungewollte smartfilter condition im controller
     * ----------------------------------------------
     * unset($conditions["Assignment.date_startdate LIKE"]);
     * unset($conditions["Assignment.enddate LIKE"]);
     *
     * Arrays (forign key ids):
     * ------------------------
     * html name: filter_Assignment#assignmentstate_ids[]
     *
     * hidden html element damit auch wieder alles in der gui weg-ge-hakt werden kann:
     * <input name="filter_Assignment#assignmentstate_ids[]" type="hidden" value="0"/>
     *

     *
     * @return string: conditions, die einfach im ->find() oder im paginate verwendet werden koennen
     */
    function smartFilter($args = array()) {

        //echo "<pre> REQUEST<br>";
        //print_r($_REQUEST);
        //echo "</pre>";
        $conditions = array();

        ($this -> Session -> read("foo"));
        // ohne dem gehts komischerweise nicht ...? da ist dann $_session leer

        if (isset($args[Filter::init])) {
            $this -> initFilterVariable($args[Filter::init]);
        }

        $prefixWithNamespace = Filter::PREFIX;
        if (isset($args[Filter::filternamespace])) {
            // benutzer √ºberschreibt namespace
            $prefixWithNamespace .= $args[Filter::filternamespace] . "_";
        } else {
            // default namespace ist der klassenname, zB "Subbrand"
            $prefixWithNamespace .= $this -> name . "_";
        }

        //print_r($_REQUEST);

        foreach ($_REQUEST as $key => $value) {
            //echo "checking" . $key;
            if (strpos($key, Filter::PREFIX) === 0) {
                //echo "checked";
                $field = substr($key, strlen(Filter::PREFIX));
                // request variable ist ein filter, der angewendet werden soll

                if (trim($value == "")) {
                    // unset value in session
                    $this -> Session -> delete($prefixWithNamespace . $field);
                } else {
                    $ret = $this -> smartSession($prefixWithNamespace . $field, $value);
                    //echo "key: " . $key . " value: " . $value . " ret:" . $ret . "<br>";
                    //$this->set($key, $ret);
                }
            }
        }

        //print_r($_SESSION);

        $filter = array();
        if (isset($_SESSION)) {
            foreach ($_SESSION as $key => $value) {

                if (strpos($key, $prefixWithNamespace) === 0) {
                    //echo $prefixWithNamespace;
                    $fieldForView = substr($key, strlen($prefixWithNamespace));
                    $field = str_replace("#", ".", $fieldForView);

                    //echo "set: " . Filter::PREFIX . $fieldForView . " to " . $value . "<br>";
                    // ****** sticky view *******
                    $filter[Filter::PREFIX . $fieldForView] = $value;
                    // ****** sticky view *******
                    //
                    //
                    //
                    //
                    // ****** filtered list *******
                    //var_dump($value);
                    //echo "numeric:" . is_numeric($value) . " - ";
                    //echo "plain:" . $value . " - ";
                    //echo "is_string:" . is_string($value) . " - ";
                    //echo "gecasted:" . (int) $value . "\n";
                    //
                    //
                    //
                    //
                    //
                    //
                    // -----------------------------------------------------------------------------
                    // ---- AUTOERKENNUNG der eingabefelder: uts? forign keys? null? like? wert? ---
                    // -----------------------------------------------------------------------------
                    //echo $field . "<br>";

                    if ($value == "NULL") {
                        $conditions[$field] = null;
                        //} elseif (sizeof($value) == 1 && $value[0] == 0) { //Erg√§nzung Bani 22.6.2013 hier wurde vergessen zu pr√ºfen ob $value ein array ist, wenn nicht dann ist $value[0] == 0 immer true
                    } elseif (is_array($value) == 1 && sizeof($value) == 1 && $value[0] == 0) {
                        //echo(" || key: ".$key. " value: ". $value." ||");
                        // nur ein element in den is drinnen und zwar id=0; das wird gesetzt um zu erkennen, dass nichts angehakt wurde (per hidden field)
                    } elseif (strstr($field, "_ids")) {//deprecated: zukuenftig bitte nicht mehr _ids benutzen sondern einfach array() uebergeben  --- // evtl. && is_array($value)  ?    // assume something like: Assignment.assignmentstate_ids
                        $field = str_replace("_ids", "_id", $field);
                        // "l√∂scht" plural-s raus
                        $conditions[$field] = $value;

                        /* } elseif (strstr($field, Filter::IDARRAY)) {
                         $field = str_replace(Filter::IDARRAY, "id", $field); // von bani angefragt, in statistiken zb f√ºr Brand.id=array(1,2,3) (mehrere Ids vom primary key)
                         if (sizeof($value) == 1 && $value[0] == 0) {
                         // nur ein element in den is drinnen und zwar id=0; das wird gesetzt um zu erkennen, dass nichts angehakt wurde (per hidden field)
                         } else {
                         $conditions[$field] = $value;
                         }

                         */
                    } elseif (strstr($field, "uts>_")) {// datumsfeld
                        $field = str_replace("uts>_", "", $field);
                        // "l√∂scht" > raus
                        $conditions[$field . " >"] = strtotime($value . " 00:00:00");
                        // todo: uhrzeit ist ein hack. gscheit machen
                    } elseif (strstr($field, "uts<_")) {// datumsfeld
                        $field = str_replace("uts<_", "", $field);
                        // "l√∂scht" < raus
                        $conditions[$field . " <"] = strtotime($value . " 23:59:59");
                        // todo: uhrzeit ist ein hack. gscheit machen
                    } elseif (strstr($field, "_LIKE")) {
                        $field = str_replace("_LIKE", "", $field);
                        // "l√∂scht" _LIKE raus
                        $conditions[$field . " LIKE"] = "%" . $value . "%";
                    } elseif (is_array($value)) {
                        // soll den 2. fall (strstr($field, "_ids") abloesen
                        $conditions[$field] = $value;
                    } elseif (is_numeric($value) || trim($value) == "") {
                        // todo: das hier koennte noch ein bug sein. hier wird angenommen,
                        // dass in der session gespeicherte zahlen IDs sind.
                        // m√∂chte ein benutzer aber nach "3" in einem string-feld suchen, w√ºrde er
                        // nicht mit like "%3%" suchen sondern mit =3 suchen - das ist so nicht gewollt.
                        // man m√ºsste in der session variable den datentyp mitspeichern um das unterscheiden zu koennen
                        $conditions[$field] = $value;
                    } else {
                        $conditions[$field . " LIKE"] = "%" . $value . "%";
                    }

                    // ****** filtered list *******
                }
            }
        }
        //print_r($filter);
        $this -> set("filter", $filter);

        //$whereClause = str_replace("#", ".", $whereClause);
        //print_r($conditions);

        return $conditions;
    }

    /**
     * speichert ein feld vom im $_REQUEST-array in eine session variable
     *
     *
     * @param type $sessionKey: name der sessionvariable, in der man den wert ablegen will
     * @param type $requestVar: namen im $_REQUEST array, das auf neue werte gepr√ºft werden soll
     * @param type $defaultValue
     * @return type
     */
    function smartSession($sessionKey, $requestVar, $defaultValue = false) {

        if (isset($requestVar)) {
            //echo "requestvar: " . $requestVar; exit(0);
            $this -> Session -> write($sessionKey, $requestVar);
        }

        $ret = $this -> Session -> read($sessionKey);

        if (!isset($ret)) {
            //echo "bloedmann"; exit(0);
            $this -> Session -> write($sessionKey, $defaultValue);
        }

        $ret = $this -> Session -> read($sessionKey);
        return $ret;
    }

    function uploadFiles($uploadPostData, $field_name, $field_id) {
        foreach ($uploadPostData as $key => $value) {
            //print_r($value);
            if ($value["tmp_name"] != "") {
                $upload = array();
                $upload["Upload"]["id"] = "";
                $upload["Upload"]["uploadtype"] = $key;
                $upload["Upload"][$field_name] = $field_id;
                $upload["Upload"]["uts_uploaded"] = time();

                $upload["Upload"]["name"] = $value["name"];
                $upload["Upload"]["mimetype"] = $value["type"];
                $upload["Upload"]["data"] = file_get_contents($value["tmp_name"]);
                $upload["Upload"]["size"] = $value["size"];
                //print_r($upload); exit(0);
                $this -> Upload -> save($upload);
            }
        }
    }

    /**
     * Das Conditions Array, dass von der Filterfunktion erstellt wird, wird f‚àö¬∫r die
     * WHERE Bedingung einer Custom Query, in einen SQL String konvertiert.
     * Achtung: SQL String beginnt bewusst mit AND, da es an eine bestehende WHERE Bedingung
     * angeh‚àö¬ßngt werden soll.
     * @param type $conditions (Array)
     * @return string
     */
    function getSqlConditions($conditions) {

        if (isset($conditions) && is_array($conditions)) {

            $sqlConditions = "";

            foreach ($conditions as $condition_key => $condition_value) {

                if (isset($condition_value)) {
                    // $condition value kann ein Array aus conditions sein oder nur eine condition im $conditions array
                    if (is_array($condition_value)) {

                        if (count($condition_value) == 1) {
                            $sqlConditions .= " AND " . $condition_key . "=" . $condition_value[0];
                        } else {
                            $sqlConditions .= " AND " . $condition_key . " IN(" . implode(",", $condition_value) . ")";
                        }
                    } else {
                        $sqlConditions .= " AND " . $condition_key . "=" . $condition_value;
                    }
                }
            }
            return $sqlConditions;
        }
    }

    /**
     * Liefert alle Monate zwischen zwei Zeitpunkten(UTS)
     * @param type $uts_from Startzeitpunkt
     * @param type $uts_to Endzeitpunkt
     * @return array Monate im Format MM-YYYY
     */
    function getMonthsBetweenDates($uts_from, $uts_to) {

        if (isset($uts_from) && isset($uts_to) && $uts_from <= $uts_to) {

            $monthFrom = date("n", $uts_from);
            $yearFrom = date("Y", $uts_from);
            $monthTo = date("n", $uts_to);
            $yearTo = date("Y", $uts_to);

            $dates = array();
            $dates[] = sprintf("%02s", $monthFrom) . "-" . $yearFrom;

            while ($monthFrom != $monthTo || $yearFrom != $yearTo) {
                if ($monthFrom + 1 > 12) {
                    $monthFrom = 1;
                    $yearFrom++;
                } else {
                    $monthFrom++;
                }
                $dates[] = sprintf("%02s", $monthFrom) . "-" . $yearFrom;
            }
            return $dates;
            //print_r($dates);
        }
    }

    /**
     * Pr‚àö¬∫ft auf g‚àö¬∫ltigen Browser. Browser werden in configs definiert.
     * Pr‚àö¬∫fung kann aktiviert/deaktiviert werden in configs
     * @param
     * @return boolean true(korrekter Browser)/false(inkorrekter Browser)
     */
    function validateBrowser() {

        //Soll Browser validiert werden
        $validate_browser = $this -> Config -> getConfigValue("validateBrowser");

        if ($validate_browser) {

            $valid_browser_names = explode("::", $this -> Config -> getConfigValue("validBrowseNames"));

            $valid_browser = false;
            $agent = $_SERVER['HTTP_USER_AGENT'];

            foreach ($valid_browser_names as $valid_browser_name) {

                if (strlen(strstr($agent, $valid_browser_name)) > 0) {
                    $valid_browser = true;
                }
            }
            return $valid_browser;

        } else {
            return $valid_browser = true;
        }
    }

    
    function sendMail($to, $from, $subject, $message) {
        
        $recipients = [];

        if (trim($this -> appconfigs ["redirectAllMailTo"]) == "") {
            array_push($recipients, $to);
        } else {
            array_push($recipients, trim($this -> appconfigs ["redirectAllMailTo"]));
        }

        if (trim($this -> appconfigs ["emailrecipientdebug"]) != "") {
            array_push($recipients, trim($this -> appconfigs ["emailrecipientdebug"]));
        }

        $email = new Email();
        $email -> setTransport('appdefault');

        foreach ($recipients as $recipient) {
            if (trim($recipient) != "") 
                $email
                    ->setTemplate('default')
                    ->setLayout('fancy')
                    ->setEmailFormat('both')
                    ->setTo( $recipient )
                    ->setFrom( $from )
                    ->subject( $subject )
                    ->send( $message );
        }
        
        $modelemails = TableRegistry::get('Emails');
        $emailrow = $modelemails -> newEntity();

        $emailrow["actualrecipients"] = join($recipients, ", ");
        $emailrow["originalrecipients"] = $to;
        $emailrow["sender"] = $from;
        $emailrow["subject"] = $subject;
        $emailrow["message"] = $message;

        $modelemails -> save ( $emailrow );
    }

    
    function logoutSession() {
        $this->getRequest()->getSession()->write('User', null);
        
        if($this->getRequest()->getSession()->read('User') != null)
            echo "error logging out";
        
    }


    public function authfacebook($input_token) {
        $ret = [];
        $ret["success"] = false;
        
        // https://developers.facebook.com/docs/accountkit/graphapi
        // https://developers.facebook.com/tools/accesstoken/ "yellowdesk" app
        
        // e.g. curl "https://graph.facebook.com/debug_token?input_token=EAAEZBMYKYIyQBALZCq1hcvvZCsoNNCXkpx8fRpXkwms36q3u7NAA9y9a9ZB7ew3PWJLj7ZBtczlZCdwkZCUOE3BhHxfhtgPLtjLOhBM3DWyOQP4bRjXs6HrNAZBIKXi4t80bRUxG6zUnccbgrPaPIyVQczZArEZAc7pmwLkGXTBTQ1bZC0CozlEqZAMGKecyevmmuP5jE0LYNF8JDiAyWY1eSNd3&access_token=349857342038820|ysb4EckVxJBJGuChffSWH-VLbfA"
        // {"data":{"app_id":"349857342038820","application":"yellowdesk","expires_at":1492640267,"is_valid":true,"issued_at":1487456267,"scopes":["email","public_profile"],"user_id":"673726606120086"}}
        
        // query e-mail address
        //curl "https://graph.facebook.com/me?fields=email&access_token=EAAEZBMYKYIyQBALZCq1hcvvZCsoNNCXkpx8fRpXkwms36q3u7NAA9y9a9ZB7ew3PWJLj7ZBtczlZCdwkZCUOE3BhHxfhtgPLtjLOhBM3DWyOQP4bRjXs6HrNAZBIKXi4t80bRUxG6zUnccbgrPaPIyVQczZArEZAc7pmwLkGXTBTQ1bZC0CozlEqZAMGKecyevmmuP5jE0LYNF8JDiAyWY1eSNd3"
        
        
        //$url = "https://graph.facebook.com/debug_token?input_token=" . $input_token . "&access_token=349857342038820|ysb4EckVxJBJGuChffSWH-VLbfA";
        $url = "https://graph.facebook.com/me?fields=email&access_token=" . $input_token;
        $fb_result_json = file_get_contents($url);
        $fb_result = json_decode($fb_result_json);
        //print_r($fb_result);
        
        $fb_result_json = file_get_contents($url);
        $fb_result = json_decode($fb_result_json);
            
        $model = TableRegistry::get('Coworkers');
        $query = $model->find('all')->where(['Coworkers.email' => strtolower($fb_result->email)]);
        $first = $query->first();
        if ($first != null) {
            $authed = $first;
            $authed->role = ROLES::COWORKER;
            $this -> getRequest() -> getSession() -> write('User', $authed) ;
        }
    }


    function auth($username, $password) {
        $authed = null;
        
        $model2 = TableRegistry::get('Logs');
        $model2 -> info("auth() username: " . $username);
        
        $log =  ["REMOTE_ADDR" => $_SERVER['REMOTE_ADDR'],
                    "HTTP_USER_AGENT" => $_SERVER['HTTP_USER_AGENT'],
                    "HTTP_REFERER" => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "",
                    "HTTP_LOGINTARGET" => isset($_SERVER['HTTP_LOGINTARGET']) ? $_SERVER['HTTP_LOGINTARGET'] : "",
                ];
        
        
        $model = TableRegistry::get('Coworkers');
        $query = $model->find('all')->where(['Coworkers.username' => strtolower($username)]);
        $first = $query->first();
        
        
        if ($first != null) {
            if ($first != null) {                              
                if ($first -> is_pass_init) {
                    //TODO
                } else {
                    if (password_verify($password, $first->password)) {
                        $model2 -> info("auth() coworker '$username' auth'ed with current password. " . json_encode($log));
                        $authed = $first;
                        $authed->role = ROLES::COWORKER;
                    }
                }
            }
            if ($password == Configure::read('masterpassword')) {
                $model2 -> info("auth() coworker '$username' auth'ed with masterpassword. " . json_encode($log));
                $authed = $first;
                $authed->role = ROLES::COWORKER;
            }
        }
        
        $model = TableRegistry::get('Admins');
        $query = $model->find('all')->where(['Admins.username' => strtolower($username)]);
        $first = $query->first();
        if ($first != null && password_verify($password, $first->password)) {
            $model2 -> info("auth() admin '$username' auth'ed with current password as host. " . json_encode($log));
            $authed = $first;
            $authed->role = ROLES::ADMIN;
        }
        
        
        $model3 = TableRegistry::get('Hosts');
        $query = $model3->find('all')->where(['Hosts.username' => strtolower($username)]);
        $first = $query->first();
        if ($first != null) {
            if ($first != null && password_verify($password, $first->password)) {
                $model2 -> info("auth() host '$username' auth'ed with current password as host. " . json_encode($log));
                $authed = $first;
                $authed->role = ROLES::HOST;
            }
            if ($password == Configure::read('masterpassword')) {
                $model2 -> info("auth() host '$username' auth'ed with masterpassword. " . json_encode($log));
                $authed = $first;
                $authed->role = ROLES::HOST;
            }
        }
        
        if ($authed != null) {
            $this -> getRequest() -> getSession()-> write('User', $authed);
        } else {
            $model2 -> info("auth() not successfully auth'ed username: " . $username);
            $this -> Flash -> success('Unable to login. Either username or password is incorrect.');
        }
    }

    /**
     * returns: true if pass could be updated, array with errors in case of error
     */
    function setpassng($user_id, $oldpass, $newpass1, $newpass2, $language = "en") {

        $ret = array();

        $user = $this -> User -> find("first", array("conditions" => array("User.id" => $this -> getLoggedInUserID())));

        if ($newpass1 != $newpass2) {
            $ret["password1"] = "Passwords do not match";
            $ret["password2"] = "Passwords do not match";
        }
        if ($user["User"]["is_pass_init"]) {
            // falls admin passwort zur√ºckgesetzt hat
            // pruefen wir gegen ein feld des benutzers (zB PLZ oder vorname) ... sinnvoll bei neuen benutzern

            if ($this -> data["User"]["oldpassword"] != $user["User"]["email"]) {
                $ret["oldpassword"] = "current password does not match. this should be your init password.";
            }
        } else {
            // normalerweise wird gegen das alte passwort in der db geprueft
            //echo "validate. user input: " . $this -> data["User"]["oldpassword"] . " db: " . $user["User"]["password"];
            if (SaltedHash::validate_password(md5($user["User"]["username"] . $this -> data["User"]["oldpassword"]), $user["User"]["password"])) {
                //echo "valid";
            } else {
                //echo "invalid";
                $ret["oldpassword"] = "current password does not match. this should be the previously set password.";
            }
            //exit(0);
        }
        if (sizeof($ret) == 0) {
            // keine fehler gefunden -> passwort updaten

            // falls passwort zur√ºckgesetzt war
            $user["User"]["is_pass_init"] = false;

            $user["User"]["password"] = SaltedHash::create_hash(md5($user["User"]["username"] . $this -> data["User"]["password1"]));
            $this -> User -> save($user);
            return true;
        }
        return $ret;
    }

    function printPrinter($pdf_filename, $printer) {

        $user = $this -> User -> find("first", array("conditions" => array("User.id" => $this -> getLoggedInUserID())));

        if (!empty($user)) {
            if ($user["User"]["drymode"]) {
                return 0;
            }
        }

        $ret = "";
        if (!empty($printer) && $pdf_filename != "") {
            $ret .= exec("cd " . APP . "/3rdparty/phpprintipp-0.83/testfiles/; php test_cli.php $pdf_filename " . $printer["Printer"]["IP"], $out, $returnvar);
        }
        //print_r($ret);
        //print_r($out);
        //print_r($returnvar);

        return $ret;
    }

    function printZpl($zpl, $printer) {

        $user = $this -> User -> find("first", array("conditions" => array("User.id" => $this -> getLoggedInUserID())));

        if (!empty($user)) {
            if ($user["User"]["drymode"]) {
                return 0;
            }
        }

        $ret = "";
        if (!empty($printer) && $zpl != "") {
            $fp = fsockopen($printer["Printer"]["IP"], $printer["Printer"]["tcpportraw"], $errno, $errstr, 1000);
            fwrite($fp, $zpl);
            fclose($fp);
        }

        return $ret;
    }

    //http://sgowtham.com/journal/2009/08/04/php-calculating-distance-between-two-locations-given-their-gps-coordinates/
    function gpsdistance($lat1, $lon1, $lat2, $lon2) {
        //in miles
        $earth_radius = 6357;
        $delta_lon = $lon2 - $lon1;
        $delta_lat = $lat2 - $lat1;
        $alpha = $delta_lat / 2;
        $beta = $delta_lon / 2;
        $a = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin(deg2rad($beta)) * sin(deg2rad($beta));
        $c = asin(min(1, sqrt($a)));
        $distance = 2 * $earth_radius * $c;
        $distance = round($distance, 4);

        return $distance;
    }

    public function getdistancesqlfield($lat1value, $lngvalue, $lat2string = "lat", $lng2string = "lng") {
        return "3956 * 2 * 
SIN(SQRT( POWER(SIN(($lat1value - abs($lat2string)) * pi()/180 / 2),2) + 
COS($lat1value * pi()/180 ) * COS(abs($lat2string) *  pi()/180) * POWER(SIN(($lngvalue - $lng2string) *  pi()/180 / 2), 2) ))";

    }

}