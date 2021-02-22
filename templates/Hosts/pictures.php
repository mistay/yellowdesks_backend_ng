<h2>Pictures for <?= $row["name"]?> </h2>

<br /><br />
<?php foreach ($pictures as $picture): ?>
    <?php
        $url = $this->Url->build(["controller" => "pictures", "action" => "get", $picture->id]);
        $url100 = $this->Url->build(["controller" => "pictures", "action" => "get", $picture->id, "resolution" => "100x100"]);
        $url100cropped = $this->Url->build(["controller" => "pictures", "action" => "get", $picture->id, "resolution" => "100x100", "crop" => "true"]);
    ?>
    <a href="<?php echo $url ?>"><img alt="" src='<?php echo $url100cropped ?>' /></a>
<?php endforeach; ?>
