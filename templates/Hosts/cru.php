<h2><?= $row->isNew() ? "Add Host" : $isHost ? "My Profile" : "Edit Host" ?></h2>


<form name="form1" method="post">
    <table>
        <tr>
            <th>Name</th>
            <td><input type="text" name="name" placeholder="My Company Ltd" value='<?php echo @$row["name"] ?>' /></td>
        </tr>
        <tr>
            <th>Firstname</th>
            <td><input type="text" name="firstname" placeholder="Romy" value='<?php echo @$row["firstname"] ?>' /></td>
        </tr>
        <tr>
            <th>Lastname</th>
            <td><input type="text" name="lastname" placeholder="Sigl" value='<?php echo @$row["lastname"] ?>' /></td>
        </tr>
        <tr>
            <th>Nickname</th>
            <td><input type="text" name="nickname" placeholder="John" value='<?php echo @$row["nickname"] ?>' /></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><input type="text" name="username" placeholder="John" value='<?php echo @$row["username"] ?>' /></td>
        </tr>
        <tr>
            <th>Title</th>
            <td><input type="text" name="title" placeholder="Creative agency downtown Salzburg" value='<?php echo @$row["title"] ?>' /></td>
        </tr>
        <tr>
            <th>Included</th>
            <td><input type="text" name="details" placeholder="high speed wifi, business printer both a4 and a3, coffee" value='<?php echo @$row["details"] ?>' /></td>
        </tr>
        <tr>
            <th>Excluded</th>
            <td><input type="text" name="extras" placeholder="parking lot, photo studio equipment, plotter" value='<?php echo @$row["extras"] ?>' /></td>
        </tr>
        <tr>
            <th>Opening Instructions</th>
            <td><input type="text" name="openinginstructions" placeholder="You'll receive an e-mail with a PIN code that you can use to access the building." value='<?php echo @$row["openinginstructions"] ?>' /></td>
        </tr>
        
        <tr>
            <th>E-Mail</th>
            <td><input type="text" name="email" placeholder="johndoe@example.com" value='<?php echo @$row["email"] ?>' /></td>
        </tr>
        <tr>
            <th>VAT ID</th>
            <td><input type="text" name="vatid" placeholder="ATU123456789" value='<?php echo @$row["vatid"] ?>' /></td>
        </tr>
<?php if ($isAdmin) { ?>
        <tr>
            <th>VAT ID successfully checked (ADMIN)</th>
            <td><input type="text" name="vatid_successfully_checked" placeholder="2017-01-01" value='<?= isset($row["vatid_successfully_checked"])? date("Y-m-d", strtotime($row["vatid_successfully_checked"])) : "" ?>' /></td>
        </tr>
<?php } ?>
        <tr>
            <th>Paypal E-Mail (for sending the money)</th>
            <td><input type="text" name="paypal_email" placeholder="my_paypal_email@example.com" value='<?php echo @$row["paypal_email"] ?>' /></td>
        </tr>
        <tr>
            <th>Phone</th>
            <td><input type="text" name="phone" placeholder="+43 664 123456789" value='<?php echo @$row["phone"] ?>' /></td>
        </tr>
        <tr>
            <th>Address</th>
            <td><input type="text" name="address" placeholder="Jakob-Haringer-Str. 3" value='<?php echo @$row["address"] ?>' /></td>
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
            <th>Address of Yellowdesk</th>
            <td>
		<input type="text" name="addressyellowdesk" placeholder="Street name and postal code where my Yellowdesk is located" value='<?php echo @$row["addressyellowdesk"] ?>' /><br />
		Additional: <?= @$row["addressadditional"] ?><br />
		GPS: <?php echo @$row["lat"] . " " . @$row["lng"]; ?> <a href="http://www.google.com/maps/place/<?= @$row["lat"] ?>,<?= @$row["lng"]?>" target="_blank">lookup GPS coords</a>
            </td>
        </tr>
        <tr>
            <th>Price for 2 Hours</th>
            <td><input type="text" name="price_2hours" placeholder="120.50" value='<?php echo @$row["price_2hours"] ?>' /></td>
        </tr>
        <tr>
            <th>Price for 1 Day</th>
            <td><input type="text" name="price_1day" placeholder="25.00" value='<?php echo @$row["price_1day"] ?>' /></td>
        </tr>
        <tr>
            <th>Price for 10 Days</th>
            <td><input type="text" name="price_10days" placeholder="215.00" value='<?php echo @$row["price_10days"] ?>' /></td>
        </tr>
        <tr>
            <th>Price for 1 Month</th>
            <td><input type="text" name="price_1month" placeholder="309.00" value='<?php echo @$row["price_1month"] ?>' /></td>
        </tr>
        <tr>
            <th>Price for 6 Months</th>
            <td><input type="text" name="price_6months" placeholder="1800.00" value='<?php echo @$row["price_6months"] ?>' /></td>
        </tr>
        <tr>
            <th>Dogs welcome</th>
            <td><input type="text" name="dogswelcome" placeholder="1" value='<?php echo @$row["dogswelcome"] ?>' /></td>
        </tr>
        <tr>
            <th>Desks</th>
            <td><input type="text" name="desks" placeholder="3" value='<?php echo @$row["desks"] ?>' /></td>
        </tr>
        <tr>
            <th>Open Monday From</th>
            <td><input type="text" name="open_monday_from" placeholder="08:30:00" value='<?php echo $row->open_monday_from == null ? "" : date("H:i:s", strtotime($row["open_monday_from"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Monday Until</th>
            <td><input type="text" name="open_monday_till" placeholder="18:30:00" value='<?php echo $row->open_monday_till == null ? "" : date("H:i:s", strtotime($row["open_monday_till"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Tuesday From</th>
            <td><input type="text" name="open_tuesday_from" placeholder="08:30:00" value='<?php echo $row->open_tuesday_from == null ? "" : date("H:i:s", strtotime($row["open_tuesday_from"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Tuesday Until</th>
            <td><input type="text" name="open_tuesday_till" placeholder="18:30:00" value='<?php echo $row->open_tuesday_till == null ? "" : date("H:i:s", strtotime($row["open_tuesday_till"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Wednesday From</th>
            <td><input type="text" name="open_wednesday_from" placeholder="08:30:00" value='<?php echo $row->open_wednesday_from == null ? "" : date("H:i:s", strtotime($row["open_wednesday_from"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Wednesday Until</th>
            <td><input type="text" name="open_wednesday_till" placeholder="18:30:00" value='<?php echo $row->open_wednesday_till == null ? "" : date("H:i:s", strtotime($row["open_wednesday_till"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Thursday From</th>
            <td><input type="text" name="open_thursday_from" placeholder="08:30:00" value='<?php echo $row->open_thursday_from == null ? "" : date("H:i:s", strtotime($row["open_thursday_from"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Thursday Until</th>
            <td><input type="text" name="open_thursday_till" placeholder="18:30:00" value='<?php echo $row->open_thursday_till == null ? "" : date("H:i:s", strtotime($row["open_thursday_till"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Friday From</th>
            <td><input type="text" name="open_friday_from" placeholder="08:30:00" value='<?php echo $row->open_friday_from == null ? "" : date("H:i:s", strtotime($row["open_friday_from"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Friday Until</th>
            <td><input type="text" name="open_friday_till" placeholder="18:30:00" value='<?php echo $row->open_friday_till == null ? "" : date("H:i:s", strtotime($row["open_friday_till"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Saturday From</th>
            <td><input type="text" name="open_saturday_from" placeholder="08:30:00" value='<?php echo $row->open_saturday_from == null ? "" : date("H:i:s", strtotime($row["open_saturday_from"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Saturday Until</th>
            <td><input type="text" name="open_saturday_till" placeholder="18:30:00" value='<?php echo $row->open_saturday_till == null ? "" : date("H:i:s", strtotime($row["open_saturday_till"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Sunday From</th>
            <td><input type="text" name="open_sunday_from" placeholder="08:30:00" value='<?php echo $row->open_sunday_from == null ? "" : date("H:i:s", strtotime($row["open_sunday_from"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open Sunday Until</th>
            <td><input type="text" name="open_sunday_till" placeholder="18:30:00" value='<?php echo $row->open_sunday_till == null ? "" : date("H:i:s", strtotime($row["open_sunday_till"])) ?>' /></td>
        </tr>
        <tr>
            <th>Open 24/7 For Fixworkers</th>
            <td><input type="checkbox" name="open_247fixworkers" <?php echo $row["open_247fixworkers"]? "checked='checked'" : "" ?> /></td>
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
