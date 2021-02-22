<!DOCTYPE html>
<html>
<head>
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

    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= "Yellowdesks" ?> :: <?= $this->fetch('title') ?>
    </title>
    <link rel="icon" type="image/jpeg" href="/favicon.jpg" />
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    
    <?= $this->Html->css('yellowdesks.css') ?>

    <?= $this->Html->css('../fonts/eraser/stylesheet.css') ?>
    <?= $this->Html->css('../fonts/din1451/stylesheet.css') ?>
    <?= $this->Html->css('menu.css') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <?= $this->Html->script('yellowdesks.js'); ?>
    <?= $this->Html->script('menu.js'); ?>
    <style>
        <?php if ($_SERVER['SERVER_NAME'] == "localhost") { ?>
        .devheader {
            background-color: red;
            font-size: 50px;
            text-align: center;
        }
        <?php } ?>
    </style>
</head>
<body>
<?php if ($_SERVER['SERVER_NAME'] == "localhost") { ?>
    <div class="devheader" >this is the development system!!</div>
<?php } ?>
    <div class="burger"><img src="<?= $this->Url->build("/img/burger.png"); ?>" /></div>
    <div class="header eraser"><a href="<?= $this->Url->build("/"); ?>">Yellowdesks</a></div>
    <div class="subheader din"><a href="<?= $this->Url->build("/"); ?>">find workspace near you</a></div>
    <div class="subsubheader">a <a href="http://coworkingsalzburg.com">coworkingsalzburg.com</a> startup</div>
    <div class="mobilemenu"></div>
    <div style="float:right; padding-right: 15px"><?= $loggedInAs; ?></div>
    <div style="clear:both"></div>
    <?= $this->Flash->render() ?>
    
    <div style="display: flex;">
        <div class="menunavdesktopanchor" ></div>
<?php if ($this->getRequest()->getSession()->read('User') != null) { ?>
        <nav class="menunav" style="min-width: 170px">
<?php if($loggedInUser != null) { ?>
            <ul class="menu">
<?php $loggedinuser = $this->getRequest()->getSession()->read('User'); ?>
<?php if ($loggedinuser -> role == "ADMIN") { ?>
                <a href="<?= $this->Url->build(["controller" => "hosts"]); ?>"><li>Hosts</li></a>
                <a href="<?= $this->Url->build(["controller" => "coworkers"]); ?>"><li>Coworkers</li></a>
                <a href="<?= $this->Url->build(["controller" => "holidays"]); ?>"><li>Holidays</li></a>
                <a href="<?= $this->Url->build(["controller" => "payments"]); ?>"><li>Payments</li></a>
                <a href="<?= $this->Url->build(["controller" => "paypals"]); ?>"><li>PayPal</li></a>
                <a href="<?= $this->Url->build(["controller" => "bookings"]); ?>"><li>Bookings</li></a>
                <a href="<?= $this->Url->build(["controller" => "configs"]); ?>"><li>Configs</li></a>
                <a href="<?= $this->Url->build(["controller" => "pictures"]); ?>"><li>Pictures</li></a>
                <a href="<?= $this->Url->build(["controller" => "videos", "action" => "index", " " /* enforce index */]); ?>"><li>Videos</li></a>
                <a href="<?= $this->Url->build(["controller" => "termsandconditions"]); ?>"><li>TaC</li></a>
                <a href="<?= $this->Url->build(["controller" => "logs"]); ?>"><li>Logs</li></a>
                <a href="<?= $this->Url->build(["controller" => "emails"]); ?>"><li>Emails</li></a>
<?php } ?>
<?php if ($loggedinuser -> role == "HOST") { ?>
                <a href="<?= $this->Url->build(["controller" => "hosts", "action" => "cru"]); ?>"><li>My Profile</li></a>
                <a href="<?= $this->Url->build(["controller" => "hosts", "action" => "cruyd"]); ?>"><li>My Yellowdesk</li></a>
                <a href="<?= $this->Url->build(["controller" => "pictures", "action" => "index"]); ?>"><li>My Pictures</li></a>
                <a href="<?= $this->Url->build(["controller" => "videos", "action" => "index", " " /* enforce index */]); ?>"><li>My Videos</li></a>
                <a href="<?= $this->Url->build(["controller" => "bookings", "action" => "host"]); ?>"><li>My Bookings</li></a>
                <a href="<?= $this->Url->build(["controller" => "hosts", "action" => "map"]); ?>"><li>Map</li></a>
<?php } ?>
<?php if ($loggedinuser -> role == "COWORKER") { ?>
                <a href="<?= $this->Url->build(["controller" => "coworkers", "action" => "cru"]); ?>"><li>My Profile</li></a>
                <a href="<?= $this->Url->build(["controller" => "pictures", "action" => "index"]); ?>"><li>My Profile Picture</li></a>
                <a href="<?= $this->Url->build(["controller" => "bookings", "action" => "mybookings"]); ?>"><li>My Bookings</li></a>
                <a href="<?= $this->Url->build("/"); ?>"><li>Map</li></a>
<?php } ?>
            </ul>
<?php } ?>
            <div class="hostname"><?= gethostname(); ?></div>
        </nav>
<?php } ?>
        <div class="content">
            <?= $this->fetch('content') ?>
        </div>  
    </div>
    <footer>
    </footer>
</body>
</html>
