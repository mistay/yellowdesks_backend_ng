<?= $this->Html->css('signup.css') ?>
<?= $this->Html->script('signup.js') ?>
    
<h2><?= __("Hello! We'd like to get to know you. Please sign up.") ?></h2>

<div class="signup flexbox">
    <div class="description">
    <?= __("Your Profile stores your personal data and represents you at Yellow Desks. You can login with your credentials to either book Yellow Desks or manage your bookings.") ?>
    </div>
    <div class="signupform">
        <form name="form1" method="post">
            <table>
                <tr class="space">
                    <td><label for="companyname">Company Name</label></td>
                    <td><label for="firstname">First Name</label></td>
                </tr>
                <tr>
                    <td><input type="text" name="companyname" id="companyname" placeholder="Company Name" value="<?= @$data['companyname'] ?>"  />
                    
                    <td><input type="text" name="firstname" placeholder="First Name" value="<?= @$data['firstname'] ?>" /></td>
                </tr>
                <tr class="errorline">
                    <td>
                        <span class="check companyname"><?= __("Please note that Yellow Desks is a B2B service.") ?></span></td>
                    </td>
                </tr>
                <tr class="space">
                    <td><label for="lastname">Last Name</label></td>
                    <td><label for="email">E-Mail</label></td>
                </tr>
                
                <tr>
                    <td><input type="text" name="lastname" placeholder="Last Name" value="<?= @$data['lastname'] ?>" /></td>
                    <td><input type="text" name="email" placeholder="E-Mail" value="<?= @$data['email'] ?>" /></td>
                </tr>
                <tr class="space">
                    <td colspan="2"><label for="password">Password</label></td>
                </tr>
                <tr class="inputs">
                    <td colspan="2"><input type="password" name="password" id="password" placeholder="Password" value="<?= @$data['password'] ?>" /></td>
                </tr>
                <tr class="space">
                    <td colspan="2"><label for="spamprotect">Spam protect: 2+2 = Please enter 'four'</label></td>
                </tr>
                <tr class="inputs">
                    <td colspan="2"><input type="text" name="spamprotect" id="spamprotect" placeholder="2+2=f..." value="<?= @$data['spamprotect'] ?>" /></td>
                </tr>
                <tr class="errorline">
                    <td colspan="2">
                        <span class="check password"><?= __("Please make sure your password is at least 8 characters long") ?></span></td>
                    </td>
                </tr>
                <tr class="space">
                    <td><input type="checkbox" value="yes" name="termsandconditions" />
                        <label for="termsandconditions"><?= __("I agree to <a href={0}>Terms & Conditions</a>", $this->Url->build(["controller" => "termsandconditions", "action" => "index"])); ?></label></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" value="Sign Up"/></td>
                </tr>
            </table>
        </form>
    </div>
</div>
