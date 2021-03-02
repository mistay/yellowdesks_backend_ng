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
<?php $count=1; $total=0; ?>
<table>
    <tr>
        <th>No</th>
        <th>Booking Date</th>
        <th>Begin</th>
        <th>End</th>
        <th>Coworker</th>
        <th>Price<br />excl. VAT</th>
        <th>VAT</th>
        <th>Total<br />incl. VAT</th>
    </tr>
    <?php foreach ($rows as $row): ?>
    <tr>
        <td><?= $count++ ?></td>
        <td><?= date("d.m.Y", strtotime($row->dt_inserted)); ?></td>
        <td><?= date("d.m.Y", strtotime($row->begin)); ?></td>
        <td><?= date("d.m.Y", strtotime($row->end)); ?></td>
        <td style="width:50%"><a href="<?= $this->Url->build(["controller" => "coworkers", "action" => "profile",  $row->coworker->id]); ?>">Profile</a> <?= $row->coworker->companyname . " | " . $row->coworker->firstname . " " . $row->coworker->lastname; ?></td>
        <td><span style="white-space:nowrap;"><?= money_format('%i', $row->amount_host) ?> EUR</span></td>
        <td><span style="white-space:nowrap;"><?= money_format('%i', $row->vat_host); ?> EUR</span></td>
        <?php $subtotal = $row->amount_host + $row->vat_host; // todo: sum??? sum financially, not mathematically ?>
        <td><span style="white-space:nowrap;"><?php $total += $subtotal; echo money_format('%i', $subtotal); ?> EUR</span></td>
    </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="8" style="text-align: right;">
            <h2>SUM
            <?= money_format('%i', $total); ?> EUR
            </h2>
        </td>
    </tr>
</table>
