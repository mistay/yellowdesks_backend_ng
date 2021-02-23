<h2>Add Picture</h2>

<form name="form1" method="post" enctype="multipart/form-data">
    <input type="hidden" name="_csrfToken" autocomplete="off" value="<?= $this->request->getAttribute('csrfToken') ?>" />
    <?php $loggedinuser = $this->request->session()->read('User'); ?>
         
    <?php if ($loggedinuser -> role == "ADMIN") { ?>
        <h3>Host</h3>
        <select name="host_id">
            <option></option>
            <?php foreach ($hosts as $row): ?>
                <option value="<?= $row->id ?>"><?= $row->name . " (id: " . $row->id . ")" ?></option>
            <?php endforeach ?>
        </select>
        <h3>Coworker</h3>
        <select name="coworker_id">
            <option></option>
            <?php foreach ($coworkers as $row): ?>
                <option value="<?= $row->id ?>"><?= $row->lastname . " " . $row->firstname . " (id: " . $row->id . ")" ?></option>
            <?php endforeach ?>
        </select>
    <?php } ?>

    <br /><br />
    <?= __("You can choose one or more pictures for upload. Resolution: the more, the better (max: {0}).", min(ini_get("upload_max_filesize"), ini_get("post_max_size"))); ?>
    <input type="file" name="files[]" multiple>
    <br /><br />
    
    <br /><br />
    <input type="submit" value="save" />
    
</form>