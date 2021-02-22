<h2>Hosts (<?= $rows->count() ?>)</h2>

<a href="<?php echo $this->Url->build(["action" => "cru"]); ?>">Add</a>

<table>
    <tr>
        <th>Name</th>
        <th>Sales (calc'ed)</th>
        <th>Title</th>
        <th>Included<br />Excluded<br />Opening instructions</th>
        <th>Billing Address</th>
        <th>Yellowdesk Address</th>
        <th>Price</th>
        <th>Open</th>
        <th>Pictures</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    <?php foreach ($rows as $row): ?>
    <tr>
        <td><?= $row->name ?><br /><?= $row->firstname ?> <?= $row->lastname ?></td>
        <td>
            <?php 
                $total = 0;
                foreach ($row->payments as $payment) { $total += $payment->amount; } 
                echo money_format("%i", $total);
            ?>
        </td>
        <td><?php echo $row->title; ?></td>
        <td><?php echo substr($row->details, 0, 30) . (strlen($row->details) > 30 ? "..." : ""); ?><br />
        <?php echo substr($row->extras, 0, 30) . (strlen($row->extras) > 30 ? "..." : ""); ?><br />
        <?php echo substr($row->openinginstructions, 0, 30) . (strlen($row->openinginstructions) > 30 ? "..." : ""); ?></td>
        <td><?php echo nl2br($row->address); ?><br /><?php echo $row->postal_code; ?><br /><?php echo $row->city; ?></td>
        <td><?= nl2br($row->addressyellowdesk) ?><br /><?= $row->lat ?>,<?=  $row->lng ?> <a href="http://www.google.com/maps/place/<?= @$row["lat"] ?>,<?= @$row["lng"]?>" target="_blank">map</a></td>
        <td><?php echo money_format("%i", $row->price_2hours); ?>
        <?php echo money_format("%i", $row->price_1day); ?>
        <?php echo money_format("%i", $row->price_10days); ?>
        <?php echo money_format("%i", $row->price_1month); ?>
        <?php echo money_format("%i", $row->price_6months); ?></td>
        <td>
            <?php echo $row->open_247fixworkers ? "24/7<br />" : "" ?>
            <?php echo $row->open_monday_from == null || $row->open_monday_till == null  ?  "" : "Mon " ?>
            <?php echo $row->open_tuesday_from == null || $row->open_tuesday_till == null  ?  "" : "Tue " ?>
            <?php echo $row->open_wednesday_from == null || $row->open_wednesday_till == null  ?  "" : "Wed " ?>
            <?php echo $row->open_thursday_from == null || $row->open_thursday_till == null  ?  "" : "Thu " ?>
            <?php echo $row->open_friday_from == null || $row->open_friday_till == null  ?  "" : "Fri " ?>
            <?php echo $row->open_saturday_from == null || $row->open_saturday_till == null  ?  "" : "Sat " ?>
            <?php echo $row->open_sunday_from == null || $row->open_sunday_till == null  ?  "" : "Sun" ?>
        </td>
        <td><a href="<?php echo $this->Url->build(["action" => "pictures", $row->id]); ?>">Pictures</a></td>
        <td><a href="<?php echo $this->Url->build(["action" => "cru", $row->id]); ?>">Edit</a></td>
        <td><a onclick="return confirm('are you sure?')" href="<?php echo $this->Url->build(["action" => "delete", $row->id]); ?>">Delete</a></td>
    </tr>
    <?php endforeach; ?>
</table>
