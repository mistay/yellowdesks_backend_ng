<h2>Edit Password</h2>

<form name="form1" method="post">
    <input type="hidden" name="_csrfToken" autocomplete="off" value="<?= $this->request->getAttribute('csrfToken') ?>" />
    <table>
        <tr>
            <th>Password</th>
            <td><input type="password" name="password1" value="" /></td>
        </tr>
        
        <tr>
            <th>Password, again</th>
            <td><input type="password" name="password2" value="" /></td>
        </tr>
        
        <tr>
            <th></th>
            <td><input type="submit" value="Save" /></td>
        </tr>
    </table>
</form>