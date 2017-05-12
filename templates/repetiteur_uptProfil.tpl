<form action="index.php?p=rep&do=uptProfil" method="post" autocomplete="off">
    <h1>Modifier le profil</h1><br /><br />
    <div class="errorText">{ERROR}</div>
    
    <span style="font-style: italic;">Tous les champs sont obligatoires.</span><br /><br />
    
    <table id="repetiteurRegister">
        <tr>
            <td style="width: 200px;"><span class="{pseudoClass}">Pseudo</span></td>
            <td><input type="text" name="pseudo" value="{pseudo}" maxlength="16" /></td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom: 20px;"><span class="small">(minimum 3 caractères, maximum 16; caractères autorisés: a-z, 0-9)</span></td>
        </tr>
        <tr>
            <td style="width: 200px;"><span class="{nameClass}">Nom</span></td>
            <td><input type="text" name="name" value="{name}" maxlength="32" /></td>
        </tr>
        <tr>
            <td style="width: 200px;"><span class="{forenameClass}">Prénom</span></td>
            <td><input type="text" name="forename" value="{forename}" maxlength="32" /></td>
        </tr>
        <tr>
            <td style="width: 200px; padding-bottom: 20px;"><span class="{emailClass}">Email</span><br /><span class="small">(seules les adresses @epfl.ch et @a3.epfl.ch sont acceptées)</span></td>
            <td><input type="text" name="email" value="{email}" maxlength="128" /></td>
        </tr>
        
        <tr>
            <td style="width: 200px; padding-bottom: 20px;"><span class="{sectionClass}">Section</span></td>
            <td>
                <select name="section">
                <!-- BEGIN sectionOpt -->
                <option value="{sectionOpt.value}" {sectionOpt.selected}>{sectionOpt.caption}</option>
                <!-- END sectionOpt -->
                </select>
            </td>
        </tr>
        
        <tr>
            <td style="width: 200px; padding-bottom: 20px;"><span class="{levelStudiesClass}">Niveau d'étude</span></td>
            <td>
                <select name="levelStudies">
                <!-- BEGIN levelStudiesOpt -->
                <option value="{levelStudiesOpt.value}" {levelStudiesOpt.selected}>{levelStudiesOpt.caption}</option>
                <!-- END levelStudiesOpt -->
                </select>
            </td>
        </tr>
    </table>
    
    <br />
    <input type="submit" name="submit" value="Modifier le profil" style="position: relative; left: 50px;" />
</form>


