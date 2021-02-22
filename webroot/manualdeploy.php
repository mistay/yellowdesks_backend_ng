<pre>
<?php
$out="";
exec("/opt/deploy.sh", $out);

var_dump($out);
?>
