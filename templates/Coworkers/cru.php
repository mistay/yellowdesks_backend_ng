<h2><?php echo $row->isNew() ? "Add" : "Edit" ?> Coworker</h2>

<form name="form1" method="post">
    <table>
        <tr>
            <th>Company Name</th>
            <td><input type="text" name="companyname" placeholder="John Doe Inc" value='<?php echo @$row["companyname"] ?>' /></td>
        </tr>
        <tr>
            <th>Firstname</th>
            <td><input type="text" name="firstname" placeholder="John" value='<?php echo @$row["firstname"] ?>' /></td>
        </tr>
        <tr>
            <th>Lastname</th>
            <td><input type="text" name="lastname" placeholder="Doe" value='<?php echo @$row["lastname"] ?>' /></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><input type="text" name="username" placeholder="johndoe" value='<?php echo @$row["username"] ?>' /></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><input type="text" name="address" placeholder="Long Street 13" value='<?php echo @$row["address"] ?>' /></td>
        </tr>
        <tr>
            <th>Postal Code</th>
            <td><input type="text" name="postal_code" placeholder="5020" value='<?php echo @$row["postal_code"] ?>' /></td>
        </tr>
        <tr>
            <th>City</th>
            <td><input type="text" name="city" placeholder="Salzburg" value='<?php echo @$row["city"] ?>' /></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><input type="text" name="phone" placeholder="+43 662 890000" value='<?php echo @$row["phone"] ?>' /></td>
        </tr>
        <tr>
            <th>VAT ID</th>
            <td><input type="text" name="vatid" placeholder="ATU123456" value='<?php echo @$row["vatid"] ?>' /></td>
        </tr>
        <tr>
            <th>VAT ID successfully checked</th>
            <td><input type="text" name="vatid_successfully_checked" placeholder="2017-01-01" value='<?php echo @$row["vatid_successfully_checked"] ?>' /></td>
        </tr>
        <tr>
            <th>E-Mail</th>
            <td><input type="text" name="email" placeholder="john@example.com" value='<?php echo @$row["email"] ?>' /></td>
        </tr>
        <tr>
            <th></th>
            <td>
            <?php if ($row->isNew()) { ?>
                Password can be changed after coworker is registered.
            <?php } else { ?>
                <a href="<?= $this->Url->build(["action" => "changepass", $row["id"]]); ?>">Change Password</a>
            <?php } ?>
            </td>
        </tr>
        <tr>
            <th></th>
            <td><input type="submit" value="Save" /></td>
        </tr>
    </table>
</form>
