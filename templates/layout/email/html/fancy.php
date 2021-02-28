<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
<head>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
    <style>
        .header {
            background-color: #f3ed3d; 
            padding-top: 20px;
            padding-bottom: 20px;
            padding-left: 20px; 
            font-size: 40px;
        }
        .header a {
            color: black;
            text-decoration: none;
        }
        body, html {
            font-family: 'Open Sans', 'Helvetica Neue', Helvetica, sans-serif;
        }
        body {
            border: 0 none;
            font-family: "din";
            font-weight: 100;
            margin: 0;
            padding: 0;
        }
        .content {
            font-size: 18px;
        }
    </style>
    <title><?= $this->fetch('title') ?></title>
</head>
<body>
    <div class="header"><a href="<?= $this->Url->build("/"); ?>">YELLOW DESKS</a></div>
    <div class="content">
        <table  width="100%" cellpadding="0" cellspacing="0" style="" >
            <tr><td>&nbsp;<br></td><td></td><td></td></tr>
    <tr>
        <td style="width:20px;" >&nbsp;</td>
        <td valign="middle" style="padding-top:6px;padding-bottom:6px;padding-right:0;padding-left:0;" >
            <?= $this->fetch('content') ?>
        </td>
        <td style="width:20px;" >&nbsp;</td>
    </tr>
</table>
    </div>
</body>
</html>
