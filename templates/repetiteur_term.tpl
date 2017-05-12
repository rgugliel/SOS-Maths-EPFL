<form action="index.php?p=rep&do=term&term={term}" method="post" autocomplete="off">
    <h1>Inscription pour le semestre {termLib}</h1><br /><br />
    <div class="errorText">{ERROR}</div>
    
    <span style="font-style: italic;">Les champs dont le nom est suivi par un * sont obligatoires.</span><br /><br />
    
    <br /><hr /><br />
    <h3>Informations personnelles</h3>
    
    <table class="repetiteurTermBlock"> 
        <tr>
            <td style="width: 200px;"><span class="{levelStudiesClass}">Semestre d'études*</span><br /><span class="small">Mettez à jour votre niveau d'études ici.</span></td>
            <td style="padding-bottom: 40px;">
                <select name="levelStudies">
                <!-- BEGIN levelStudiesOpt -->
                <option value="{levelStudiesOpt.value}" {levelStudiesOpt.selected}>{levelStudiesOpt.caption}</option>
                <!-- END levelStudiesOpt -->
                </select>
            </td>
        </tr>

        <tr>    
            <td style="width: 200px;"><span class="{disponibleClass}">Disponible</span></td>
            <td><input type="checkbox" name="available" value="1" {availableChecked} /></td>
        </tr>    
        <tr>
            <td colspan="2">     
                <span class="small">Décochez cette case si vous n'avez pas le temps pour des étudiants supplémentaires, ainsi vous n'apparaitrez plus dans les recherches.</span>
            </td>
        </tr>
    </table>
    
    <br /><hr /><br />
    
    <h3>Informations pratiques</h3>
    <table class="repetiteurTermBlock"> 
        <tr>
            <td style="width: 200px; padding-bottom: 20px;"><span class="{feeClass}">Tarif horaire*</span></td>
            <td style="padding-bottom: 40px;" id="feeRow">
                <!-- BEGIN feeOpt -->
                <input type="checkbox" name="fee-{feeOpt.value}" value="1" onclick="fee_onClick( );" {feeOpt.checked}> {feeOpt.caption}<br />
                <!-- END feeOpt -->
            </td>
        </tr>
        
        <tr>
            <td style="width: 200px;"><span class="{placeClass}">Lieu du cours*</span></td>
            <td>
            <!-- BEGIN placeOpt -->
            <input type="checkbox" name="place-{placeOpt.value}" value="1" {placeOpt.checked}> {placeOpt.caption}<br />
            <!-- END placeOpt -->
            </td>
        </tr>

        <tr>
            <td style="width: 200px; padding-bottom: 30px;"><span class="{placeCommentClass}">Si autre, précisez</span></td>
            <td style="padding-bottom: 40px;"><input type="text" name="placeComment" value="{placeComment}" maxlength="256" /></td>
        </tr>
        
        <tr>
            <td style="width: 200px; padding-bottom: 30px;"><span class="{availabilityClass}">Disponibilités*</span></td>
            <td>
                <span class="small">Cliquez sur un jour ou une période pour cocher/décocher toute une ligne/colonne en une fois.</span>
                <table cellpadding="0" cellspacing="0">
                    <thead>
                        <tr>
                            <td>Jour</td>
                            <td onclick="availabilityDay_colOnClick( 'am' ); return false;">Matin</td>
	                        <td onclick="availabilityDay_colOnClick( 'm' ); return false;">Midi</td>
	                        <td onclick="availabilityDay_colOnClick( 'pm' ); return false;">Après-midi</td>
	                        <td onclick="availabilityDay_colOnClick( 'e' ); return false;">Soir</td>
                        </tr>
                    </thead>
                    <!-- BEGIN availabilityDay -->
                    <tr id="availabilityDayTr-{availabilityDay.dayIndex}">
                        <td class="infos{availabilityDay.tdClass}" onclick="availabilityDay_rowOnClick( 'availabilityDayTr-{availabilityDay.dayIndex}' ); return false;">{availabilityDay.dayLibelle}</td>
                        <td class="infosC{availabilityDay.tdClass}"><input type="checkbox" name="{availabilityDay.dayIndex}-am" id="{availabilityDay.dayIndex}-am" onclick="formOnChange( );" value="1" {availabilityDay.amChecked} /></td>
	                    <td class="infosC{availabilityDay.tdClass}"><input type="checkbox" name="{availabilityDay.dayIndex}-m" id="{availabilityDay.dayIndex}-m" onclick="formOnChange( );" value="1" {availabilityDay.mChecked} /></td>
	                    <td class="infosC{availabilityDay.tdClass}"><input type="checkbox" name="{availabilityDay.dayIndex}-pm" id="{availabilityDay.dayIndex}-pm" onclick="formOnChange( );" value="1" {availabilityDay.pmChecked} /></td>
	                    <td class="infosC{availabilityDay.tdClass}"><input type="checkbox" name="{availabilityDay.dayIndex}-e" id="{availabilityDay.dayIndex}-e" onclick="formOnChange( );" value="1" {availabilityDay.eChecked} /></td>
                    </tr>
                    <!-- END availabilityDay -->
                </table>
            </td>
        </tr>
    </table>    
        
    <br /><hr /><br />
        
    <h3>Enseignement</h3>
    <table class="repetiteurTermBlock"> 
        <tr>
            <td style="width: 200px;"><span class="{languageClass}">Langues dans lesquelles vous pouvez enseigner*</span></td>
            <td style="padding-bottom: 10px;">
            <!-- BEGIN languageOpt -->
            <input type="checkbox" name="language-{languageOpt.value}" value="1" {languageOpt.checked}> {languageOpt.caption}<br />
            <!-- END languageOpt -->
            </td>
        </tr>
        
        <tr>
            <td style="width: 200px;"><span class="{subjectClass}">Matières et niveaux*</span></td>
            <td>
                <input type="checkbox" name="teachingLevel-0" value="1" {teachingLevel-0Checked} /> Primaire et secondaire<br /><br />
                <input type="checkbox" name="teachingLevel-1" id="teachingLevel-1" value="1" id="teachingLevel-1" onclick="checkLevel( 1 );" {teachingLevel-1Checked} /> Gymnase<br />
                    <div style="position: relative; left: 30px;" id="teachingLevel-1Opts">
                    <!-- BEGIN teachingsubject1Opt -->
                    <input type="checkbox" name="teachingSubjects-{teachingsubject1Opt.value}" value="1" {teachingsubject1Opt.checked} onclick="if( this.checked ) $('teachingLevel-1').checked = true;" /> {teachingsubject1Opt.caption}<br />
                    <!-- END teachingsubject1Opt -->
                </div><br />
                <input type="checkbox" name="teachingLevel-2" id="teachingLevel-2" value="1" id="teachingLevel-2"  onclick="checkLevel( 2 );" {teachingLevel-2Checked} /> Université / HES / ...<br />
                <div style="position: relative; left: 30px;" id="teachingLevel-2Opts">
                    <!-- BEGIN teachingsubject2Opt -->
                    <input type="checkbox" name="teachingSubjects-{teachingsubject2Opt.value}" value="1" {teachingsubject2Opt.checked} onclick="if( this.checked ) $('teachingLevel-2').checked = true;" /> {teachingsubject2Opt.caption}<br />
                    <!-- END teachingsubject2Opt -->
                </div>
            </td>
        </tr>
    </table>
    
    <br /><hr /><br />
        
    <h3>Commentaire</h3>
    
    Vous pouvez saisir un commentaire qui sera visible lors des recherches:<br />
    <textarea name="comment" cols="60" rows="15">{comment}</textarea>
    
    <br /><br /><br />
    <input type="submit" name="submit" value="{submitLib}" style="position: relative; left: 50px;" />
</form>


