<div class="container">
    <div class="row mb">
        <div class="col-md-12">
            <h1>Login</h1>
<div class="users form">
<?= $this->Flash->render('errors') ?>
    
<?php 
    $someone = $this->getRequest()->getSession()->read('User');
    if (isset($someone->username)) {
            echo __('You are logged in as {0} with username {1}', $someone->role, $someone->username);
        
        ?>
        <a href="<?= $this->Url->build(["controller" => "users", "action" => "logout"]); ?>"><?= __('Logout'); ?></a>
        
        <?php
        
    } else {
    ?>
    <?= $this->Form->create() ?>
            <?= __('Please enter your username and password') ?>
            <br /><br />
            <?= $this->Form->input('username') ?>
            <?= $this->Form->password('password') ?>
            <a href="<?= $this->Url->build(["controller" => "users", "action" => "forgotpassword"]); ?>"><?= __('Forgot Password'); ?></a>
            <br />
            <?= $this->Form->button(__('Login')); ?>
    <?= $this->Form->end() ?>
    
    <?php } ?>
            </div></div></div></div>
