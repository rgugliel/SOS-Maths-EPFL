<form action="index.php?p=rep&do=register" method="post" autocomplete="off">
    <h1>S'inscrire</h1><br /><br />
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
            <td style="width: 200px; padding-bottom: 20px;"><span class="{emailClass}">Email</span><br /><span class="small">(seules les adresses @epfl.ch, @alumni.epfl.ch et @a3.epfl.ch sont acceptées; <a href="index.php?p=faq#repEmail" target="_blank">pourquoi?</a>)</span></td>
            <td><input type="text" name="email" value="{email}" maxlength="128" /></td>
        </tr>
        
        <tr>
            <td style="width: 200px;"><span class="{passwordClass}">Mot de passe</span><br /><span class="small">(au moins 6 caractères)</span></td>
            <td><input type="password" name="password1" value="{password1}" maxlength="32" /></td>
        </tr>
        <tr>
            <td style="width: 200px; padding-bottom: 30px;"><span class="{passwordClass}">Retapez le mot de passe</span></td>
            <td><input type="password" name="password2" value="{password2}" maxlength="32" /></td>
        </tr>
        
        <tr>
            <td style="width: 200px; padding-bottom: 20px;"><span class="{sectionClass}">Section</span></td>
            <td>
                <select name="section" onchange="if( this.options[ this.selectedIndex ].value == 'cms' ) $('levelSelect').selectedIndex = 1;" id="sectionSelect">
                <!-- BEGIN sectionOpt -->
                <option value="{sectionOpt.value}" {sectionOpt.selected}>{sectionOpt.caption}</option>
                <!-- END sectionOpt -->
                </select>
            </td>
        </tr>
        
        <tr>
            <td style="width: 200px; padding-bottom: 20px;"><span class="{levelStudiesClass}">Niveau d'étude</span></td>
            <td>
                <select name="levelStudies" id="levelSelect">
                <!-- BEGIN levelStudiesOpt -->
                <option value="{levelStudiesOpt.value}" {levelStudiesOpt.selected}>{levelStudiesOpt.caption}</option>
                <!-- END levelStudiesOpt -->
                </select>
            </td>
        </tr>
        
        <tr>
            <td style="width: 200px; padding-bottom: 20px;"><span class="{conditionsClass}">Conditions d'utilisation</span></td>
            <td><input type="checkbox" name="conditions" value="1" {conditionsChecked} /> En cochant la case ci-contre, vous attestez avoir pris connaissance des <a href="index.php?p=cdt">conditions d'utilisation de SOS-Maths</a> et déclarez les accepter.</td>
        </tr>
    </table>
    
    <br />
    {CAPTCHA}
    
    <br />
    <input type="submit" name="submit" value="S'inscrire" style="position: relative; left: 50px;" />
</form>


