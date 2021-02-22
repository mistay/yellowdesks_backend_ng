<h2><?= __("Forgot Password") ?></h2>

<div>
    <form name="form1" method="post">

        <?= __("Please provide the e-mail address that is ascociated with your profile. We'll send you a link to set a new password.") ?>
        <input type="text" name="email" placeholder="me@example.com" />

        <input type="submit" value="Request Password"/>
    </form>
</div>