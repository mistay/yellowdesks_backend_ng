<!DOCTYPE html>
<html>
<head>
    <base target="_parent">

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
    <?= $this->Flash->render() ?>
    <div style="display: flex;">
        <div class="content">
            <?= $this->fetch('content') ?>
        </div>  
    </div>
    <footer>
    </footer>
</body>
</html>
