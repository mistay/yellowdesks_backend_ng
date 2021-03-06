<?php
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Network\Exception\NotFoundException;

$this->disableAutoLayout();
?>
<!DOCTYPE html>
<html prefix="og: http://ogp.me/ns#">
    <head>
        <meta property="og:title" content="Yellowdesks" />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="https://www.yellowdesks.com" />
        <meta property="og:image" content="https://www.yellowdesks.com/img/opengraph_image_yellowdesks.jpg" />
        <meta property="og:app_id" content="349857342038820" />

	    <link rel="alternate" href="https://www.yellowdesks.com/" hreflang="en" />

        <?php //google maps renders map objects (streets, markers, ..) much bigger ?>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />

        <script>
            var googlemapsapikey = '<?= $googlemapsapikey ?>';
        </script>

        <script type="text/javascript">
            var baseurl = "<?= $this->Url->build("/", []) ?>";
        </script>

        <!-- Piwik
        <script type="text/javascript">
            var _paq = _paq || [];
            /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
            _paq.push(['trackPageView']);
            _paq.push(['enableLinkTracking']);
            (function() {
                var u="https://piwik.langhofer.at/";
                _paq.push(['setTrackerUrl', u+'piwik.php']);
                _paq.push(['setSiteId', '1']);
                var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
                g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
            })();
        </script>
        End Piwik Code -->

        <script src="js/jquery-3.5.1.min.js"></script>
        
        <?= $this->Html->css('../3rdparty/lightslider/css/lightslider.css') ?>
        <style>
            .demo {
                width: 250px;
            }
        </style>

        <?= $this->Html->script('../3rdparty/lightslider/js/lightslider.js'); ?>

        <?= $this->Html->css('main.css') ?>

        <?= $this->Html->script('home.js'); ?>
        <?= $this->Html->css('home.css') ?>

        <?= $this->Html->script('menu.js'); ?>

        <meta charset="utf-8">
        <title>Yellowdesks: workspace near you</title>
        <meta name="description" content="Yellowdesks - Workspace near you">

        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

        <link rel="stylesheet" href="fonts/eraser/stylesheet.css">
        <link rel="stylesheet" href="fonts/din1451/stylesheet.css">
        
        <script>
            <?php
                // security: only expose public fields
                $rets = [];
                foreach ($rows as $row) {
                    $ret = new stdClass();

                    if (strpos($row->nickname, "test") === 0 && ($loggedinuser == null || $loggedinuser->role != "ADMIN"))
                        continue;
                    $ret-> id = $row -> id;
                    $ret-> nickname = $row -> nickname;
                    $ret-> title = $row -> title;
                    $ret-> details = $row -> details;
                    $ret-> extras = $row -> extras;
                    $ret-> lat = $row -> lat;
                    $ret-> lng = $row -> lng;
                    $ret-> open_monday_from = $row -> open_monday_from == null ? null : date("H:i", strtotime($row -> open_monday_from));
                    $ret-> open_monday_till = $row -> open_monday_till == null ? null : date("H:i", strtotime($row -> open_monday_till));
                    $ret-> open_tuesday_from = $row -> open_tuesday_from == null ? null : date("H:i", strtotime($row -> open_tuesday_from));
                    $ret-> open_tuesday_till = $row -> open_tuesday_till == null ? null : date("H:i", strtotime($row -> open_tuesday_till));
                    $ret-> open_wednesday_from = $row -> open_wednesday_from == null ? null : date("H:i", strtotime($row -> open_wednesday_from));
                    $ret-> open_wednesday_till = $row -> open_wednesday_till == null ? null : date("H:i", strtotime($row -> open_wednesday_till));
                    $ret-> open_thursday_from = $row -> open_thursday_from == null ? null : date("H:i", strtotime($row -> open_thursday_from));
                    $ret-> open_thursday_till = $row -> open_thursday_till == null ? null : date("H:i", strtotime($row -> open_thursday_till));
                    $ret-> open_friday_from = $row -> open_friday_from == null ? null : date("H:i", strtotime($row -> open_friday_from));
                    $ret-> open_friday_till = $row -> open_friday_till == null ? null : date("H:i", strtotime($row -> open_friday_till));
                    $ret-> open_saturday_from = $row -> open_saturday_from == null ? null : date("H:i", strtotime($row -> open_saturday_from));
                    $ret-> open_saturday_till = $row -> open_saturday_till == null ? null : date("H:i", strtotime($row -> open_saturday_till));
                    $ret-> open_sunday_from = $row -> open_sunday_from == null ? null : date("H:i", strtotime($row -> open_sunday_from));
                    $ret-> open_sunday_till = $row -> open_sunday_till == null ? null : date("H:i", strtotime($row -> open_sunday_till));
                    $ret-> price_1day = $row -> price_1day;
                    $ret-> price_10days = $row -> price_10days;
                    $ret-> price_1month = $row -> price_1month;
                    $ret-> price_6months = $row -> price_6months;

                    $ret-> pictureids = [];

                    if ($row -> picture_id != null)
                        array_push($ret-> pictureids, $row -> picture_id);

                    foreach ($row -> pictures as $picture) {
                        array_push($ret-> pictureids, $picture -> id);
                    }

                    if (isset ($row -> videos[0]))
                        $ret-> videourl = $row -> videos[0] -> url;


                    array_push($rets, $ret);
                }
            ?>
            var hosts = <?= json_encode($rets); ?>;
        </script>

        <?php
            $url = $this->Url->build('/favicon.jpg', []);
        ?>
        <link rel="icon" type="image/jpeg" href="<?= $url ?>" />
    </head>
    <body>

        <style>
            .iframelightbox {
                width: 80%;
                height: 80%;
                left: 10%;
                top: 10%;
                z-index: 1000;
                position: absolute;
                display: none;
            }
            @media (max-width: 600px) {
                .iframelightbox {
                    left: 1%;
                    top: 1%;
                    width: 98%;
                    height: 98%;
                    padding-top: 55px;
                }
            }
            .iframelightbox iframe{
                width: 100%;
                height: 100%;
            }
            .iframeclose {
                right: 0px;
                padding: 7px;
                margin: 2px;
                position: absolute;
                background-color: #ececec;
                cursor: pointer;
            }
        </style>
    
        <div class="iframelightbox">
            <a class="iframeclose">CLOSE</a>
            <iframe src=""></iframe>
        </div>
        <div class="menunavdesktopanchor">
        </div>
        <div class="menunav">
            <div class="menu">
                <?php
                
                $urlroot = $this->Url->build("/");

                $urlprofile = $this->Url->build([
                        "controller" => "users",
                        "action" => "welcome",
                    ]);

                $urlbecomeahost = $this->Url->build([
                        "controller" => "users",
                        "action" => "becomeahost",
                    ]);

                $urlregister = $this->Url->build([
                        "controller" => "users",
                        "action" => "signup",
                    ]);
                
                if ($loggedinuser == null) {
                    $url = $this->Url->build([
                        "controller" => "users",
                        "action" => "login",
                    ]);
                    $loginlogouttext = __("Login");
                } else {
                    $url = $this->Url->build([
                        "controller" => "users",
                        "action" => "logout",
                    ]);
                    $loginlogouttext = __("Logout");
                }
                ?>

                <a class="questionmark" href="<?= $this->Url->build("/", []) ?>/faqs"><img src="<?= $urlroot ?>img/questionmark_bw_transparent.png" /></a>
                <a class="facebooklogo" target="_blank" href="https://www.facebook.com/yellowdesks/"><img src="<?= $urlroot ?>img/facebook_transparent.png" /></a>
                <a class="androidlogo" target="_blank" href="https://play.google.com/store/apps/details?id=com.yellowdesks.android"><img src="<?= $urlroot ?>img/android_logo_bw_transparent.png" /></a>
                
                <?php if ($loggedinuser == null) { ?>
                    <a href="<?= $urlbecomeahost ?>">Become A Host</a>
                    <a href="<?= $urlregister ?>">Sign Up</a>
                <?php } else { ?>
                    <a href="<?= $urlprofile ?>"><?= __("Profile") ?></a>
                <?php } ?>

                <a href="<?= $url ?>"><?= $loginlogouttext ?></a>
            </div>
        </div>
        <div class="burger">
            <a class="androidlogo" target="_blank" href="https://play.google.com/store/apps/details?id=com.yellowdesks.android"><img src="<?= $urlroot ?>img/android_logo_bw_transparent.png" /></a>
            <img src="<?= $this->Url->build("/img/burger.png"); ?>" />
        </div>


        <div class="content home-content" id="home-logo">
            <span class="yellowdesks">yellow desks</span>
            <div class="yellowlinks">
                <span class="findandrent"><a href="https://www.yellowdesks.com/users/login" title="become a host" alt="become a host">&gt; &gt;  <strong>Find</strong> flexible work space near you</a></span>
            </div>
            <div class="yellowlinks">
                <span class="findandrent"><a href="https://www.yellowdesks.com/users/becomeahost" title="become a host" alt="become a host">&gt; &gt; <strong>Rent out</strong> work space</a></span>
            </div>
        </div>
        
        <div class="footer"><a href="http://coworkingsalzburg.com">by <span class="coworkingsalzburg"><strong>COWORKING</strong>SALZBURG</span></a></div>
    
        <input type="text" id="pac-input" />
    
        <div class="mobilemenu"></div>
        <div id="map"></div>

        <!-- Start of Rocket.Chat Livechat Script
        <script type="text/javascript">
        (function(w, d, s, u) {
            w.RocketChat = function(c) { w.RocketChat._.push(c) }; w.RocketChat._ = []; w.RocketChat.url = u;
            var h = d.getElementsByTagName(s)[0], j = d.createElement(s);
            j.async = true; j.src = 'https://rocket.langhofer.at/packages/rocketchat_livechat/assets/rocketchat-livechat.min.js?_=201702160944';
            h.parentNode.insertBefore(j, h);
        })(window, document, 'script', 'https://rocket.langhofer.at/livechat');
        </script>
        End of Rocket.Chat Livechat Script -->

    </body>
</html>
