<h1>Modifier son mot de passe</h1>

<form action="index.php?p=rep&do=uptPsw" method="post" autocomplete="off">
    <div class="errorText">{ERROR}</div>
    
    <table>
        <tr>
            <td style="width: 225px;"><span class="{oldClass}">Mot de passe actuel</span></td>
            <td><input name="old" type="password" /></td>
        </tr>
        <tr>
            <td style="width: 225px;"><span class="{newClass}">Nouveau mot de passe</span></td>
            <td><input name="new1" type="password" /></td>
        </tr>
        <tr>
            <td style="width: 225px;"><span class="{newClass}">Retapez le nouveau mot de passe</span></td>
            <td><input name="new2" type="password" /></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;"><br /><input type="submit" name="submit" value="Changer de mot de passe" /></td>
        </tr>
    </table>
    
</form>