<style>
    .delete {
        position: absolute;
        top: -10px;
        right: -10px;
        cursor: pointer;
    }
    .delete img {
        width: 25px;
        height: 25px;
    }
    .deletewrapper {
        position: relative;
        display: inline-block;
    }
    .profilepicture {
        border: 5px solid black;
    }
</style>
<h2>Pictures</h2>

<a href="<?php echo $this->Url->build(["action" => "cru"]); ?>">Add</a>
<br /><br />
<?php foreach ($rows as $row): ?>
    <?php
        $url = $this->Url->build(["controller" => "pictures", "action" => "get", $row->id]);
        $url100 = $this->Url->build(["controller" => "pictures", "action" => "get", $row->id, "resolution" => "100x100"]);
        $url100cropped = $this->Url->build(["controller" => "pictures", "action" => "get", $row->id, "resolution" => "100x100", "crop" => "true"]);
    ?>

    <?php $profilepictureclass = (isset($host) && $host->picture_id == $row->id) ? "profilepicture" : "" ?>
    <?php $profilepictureclass = $profilepictureclass || (isset($coworker) && $coworker->picture_id == $row->id) ? "profilepicture" : "" ?>

    <div class="deletewrapper">
        <?php if ($profilepictureclass == "") { ?>
            <div onclick="return confirm('are u sure')" class="delete">
                <a href="<?= $this->Url->build(["action" => "delete", $row->id]) ?>"><img src="<?= $this->Url->build("/img/cross.png") ?>"></a>
            </div>
        <?php } ?>
        <a href="<?= $url ?>"><img class="<?= $profilepictureclass ?>" alt="" src='<?php echo $url100cropped ?>' /></a>
    </div>
<?php endforeach; ?>
