<h2>Coworkers (<?= $rows->count() ?>)</h2>

<a href="<?php echo $this->Url->build(["action" => "cru"]); ?>">Add</a>

<table>
    <tr>
        <th></th>
        <th>Firstname</th>
        <th>Lastname</th>
        <th>Username</th>
        <th>Companyname</th>
        <th>Address</th>
        <th>VATID</th>
        <th>E-Mail</th>
        <th>Edit</th>
        <th>Delete</th>
    </tr>
    <?php foreach ($rows as $row): ?>
    <tr>
        <td>
            <?php if ($row -> picture_id > 0) { ?>
                <img style="border-radius: 100px" src="<?php echo $this->Url->build(["controller" => "pictures", "action" => "get", $row->picture_id, "crop" => "true", "resolution" => "50x50"]); ?>" />
            <?php } ?>
        </td>
        <td><?php echo $row->firstname ?></td>
        <td><?php echo $row->lastname ?></td>
        <td><?php echo $row->username ?></td>
        <td><?php echo $row->companyname ?><br />
        <td><?= $row->address; ?><br /><?= $row->postal_code; ?> <?= $row->city ?><br />
        <td><?php echo $row->vatid; ?><br />
        <td><a href="mailto:<?= $row->email; ?>"><?= $row->email ?><br />
        <td><a href="<?php echo $this->Url->build(["action" => "cru", $row->id]); ?>">Edit</a></td>
        <td><a onclick="return confirm('are you sure?')" href="<?php echo $this->Url->build(["action" => "delete", $row->id]); ?>">Delete</a></td>
    </tr>
    <?php endforeach; ?>
</table>