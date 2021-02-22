<?php
if(!isset($row)) {
	echo "Sorry, you don't have permissions to see coworker's profile";
	return;
}
?>
<h2><?= $isCoworker ? "Your Profile" : "" ?><?= $isHost || $isAdmin ? "Coworker's Profile" : "" ?></h2>
            <?php if ($row -> picture_id > 0) { ?>
                <img style="border-radius: 10px" src="<?php echo $this->Url->build(["controller" => "pictures", "action" => "get", $row->picture_id, "crop" => "true", "resolution" => "250x250"]); ?>" />
            <?php } ?>
<br />
<br />

<br />
<table>
    <tr>
        <th>Companyname</th>
	<td><?= $row->companyname?></td>
    </tr>
    <tr>
        <th>Firstname</th>
	<td><?= $row->firstname ?></td>
    </tr>
    <tr>
        <th>Lastname</th>
	<td><?= $row->lastname ?></td>
    </tr>
    <tr>
	<th>E-Mail</th>
	<td><?= $row->email ?></td>
    <tr>
    </tr>
	<th>Phone</th>
	<td><?= $row->phone?></td>
    <tr>
</table>
