<h2>Booking Overview</h2>

<?php echo $host->name . " (Nickname: " . $host->nickname . ")"; ?>
<br />
<?php echo $host->address; ?>
<br />
<?php echo $host->postal_code . " " . $host->city; ?>
<br />
<?php echo $host->email; ?>
<br />


<?php
$total=0;
?>

<form name="form1" method="post">
    <table>
        <tr>
            <th>Year</th>
            <td><input name="year" type="text" value="<?= $year ?>" /></td>
        </tr>
        <tr>
            <th>Month</th>
            <td><input name="month" type="text" value="<?= $month ?>" /></td>
        </tr>
        <tr>
            <th></th>
            <td><input name="submit" type="submit" value="Submit" /></td>
        </tr>
    </table>
</form>

<br />
<br />

<table>
    <tr>
        <th>id</th>
        <th>Booking Date</th>
        <th>Begin</th>
        <th>End</th>
        <th>Coworker</th>
        <th>Price excl. VAT</th>
        <th>VAT</th>
        <th>Total</th>
    </tr>
    <?php foreach ($rows as $row): $total=0; ?>
    <tr>
        <td><?= $row -> id ?></td>
        <td><?php echo date("d.m.Y", strtotime($row->dt_inserted)); ?></td>
        <td><?php echo date("d.m.Y", strtotime($row->begin)); ?></td>
        <td><?php echo date("d.m.Y", strtotime($row->end)); ?></td>
        <td><?php echo $row->coworker->companyname . " " . $row->coworker->firstname . " " . $row->coworker->lastname; ?><br /><a href="<?= $this->Url->build(["controller" => "coworkers", "action" => "profile",  $row->coworker->id]); ?>">View Profile</a></td>
        <td><?php echo money_format('%i', $row->amount_host); ?></td>
        <td><?php echo money_format('%i', $row->vat_host); ?></td>
        <?php $subtotal = $row->amount_host + $row->vat_host; // todo: sum??? sum financially, not mathematically ?>
        <td><?php $total += $subtotal; echo money_format('%i', $subtotal); ?></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="7">
            
        </td>
        <td><h2>
            <?php echo money_format('%i', $total); ?>
            </h2>
        </td>
    </tr>
</table>
