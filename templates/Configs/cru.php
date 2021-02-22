<h2><?php echo $row->isNew() ? "Add" : "Edit" ?> Config</h2>

<form name="form1" method="post">
    <table>
        <tr>
            <th>Configkey</th>
            <td><input type="text" name="configkey" placeholder="mysetting" value='<?php echo @$row["configkey"] ?>' /></td>
        </tr>
        <tr>
            <th>Configvalue</th>
            <td><textarea type="text" name="configvalue" placeholder="myvalue"><?php echo @$row["configvalue"] ?></textarea></td>
        </tr>
        <tr>
            <th></th>
            <td><input type="submit" value="Save" /></td>
        </tr>
    </table>
</form>