<h2><?= __("Reset Password") ?></h2>

<div>
    <form name="form1" method="post">
    <input type="hidden" name="_csrfToken" autocomplete="off" value="<?= $this->request->getAttribute('csrfToken') ?>" />

        <?= __("Please provide a new password.") ?>
        <input type="password" name="password1" placeholder="mysecret" />
        <input type="password" name="password2" placeholder="mysecret" />
        
        <input type="submit" value="<?= __('Save') ?>" />
    </form>
</div>