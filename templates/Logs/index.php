<h2>Last 100 Log Entries</h2>

<a href="<?php
            echo $this->Url->build([
                "action" => "clear",
            ]);
            ?>" onclick='return confirm("<?= __("Are you sure?") ?>")'>Clear</a>
<table>
    <tr>
        <th>logged</th>
        <th>message</th>
    </tr>
    <tr>
    <?php foreach($rows as $row) { ?>
    <tr>
        <td><?= $row["ts_logged"]; ?></td>
        <td><?= $row["message"]; ?></td>
    </tr>
    <?php } ?>
</table>