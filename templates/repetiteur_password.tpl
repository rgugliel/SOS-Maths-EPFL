<form action="index.php?p=rep&do=password" method="post">
    <h1>Mot de passe perdu</h1><br /><br />
    <div class="errorText">{ERROR}</div>
    
    Veuillez remplir remplir les deux champs suivants:
    <table>
        <tr>
            <td style="width: 200px;">Pseudo</td>
            <td><input type="text" name="pseudo" value="{pseudo}" maxlength="16" /></td>
        </tr>
        <tr>
            <td style="width: 200px; padding-bottom: 20px;">Email</span><br /><span class="small">(email utilisé lors de l'inscription)</span></td>
            <td><input type="text" name="email" value="{email}" maxlength="128" /></td>
        </tr>
    </table><br />
    Si vous ne vous rappelez pas de ces informations, <a href="index.php?p=stu&do=write&id=0">contactez SOS-Maths</a>.<br /><br />
    
    Veuillez recopier les mots ci-dessous:
    {CAPTCHA}
    
    <br /><br /><br />
    <input type="submit" name="submit" value="Valider" style="position: relative; left: 50px;" />
</form>


