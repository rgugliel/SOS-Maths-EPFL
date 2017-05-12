<h1>Trouver un répétiteur</h1>

<!-- todo: dégager tout ce bordel -->
<style>
div#studentFixed
{
    position: fixed;
    top: 216px;
    
    padding: 2px;
    width: 310px;
    border: 1px solid #000;
    background-color: #fff1f1;
}

.subtitle
{
    font-weight: bold;
    font-size: 14px;
}
</style>

<script type="text/javascript">
    // todo: gérer valeurs par défaut ici ou bien dégager
    var teachingSubjects = new Array( );
    var language = new Array( 'fr' );
    var language = new Array( );
    var levelStudies = new Array( );
    var section = new Array( );
</script>

<form autocomplete="off" method="post" action="index.php?p=stu">
    
<div style="width: 100%;" id="studentContent">
	<div class="infos0" style="width: 490px; float: left; margin: 0px 25px 10px 0px;">
		<div class="errorText" id="infos">{INFOS}</div><br />
    
        <h3>Semestre</h3>
        <select name="term" id="term" onchange="formOnChange( );">
            <!-- BEGIN termOpt -->
            <option value="{termOpt.value}" {termOpt.selected}>{termOpt.caption}</option>
            <!-- END termOpt -->
        </select><br /><br />
        
        
        <span style="float: left; width: 90%;" class="subTitle" ondblclick="expandReduce('practical'); return false;"><h3>Informations pratiques</h3></span>
        <span style="float: right; margin-top: 3px; margin-right: 3px;"><a href="#" onclick="expandReduce('practical'); return false;"><img src="images/reduce.gif" id="practicalImg" /></a></span>
        <br /><br />
        
        <div style="float: left;" id="practical">
            <span class="subtitle">Lieux du cours</span><br />
            <!-- BEGIN placeOpt -->
            <input type="checkbox" name="place-{placeOpt.value}" value="1" {placeOpt.checked}> {placeOpt.caption}<br />
            <!-- END placeOpt -->
            <br /><br />
            
            <span class="subtitle">Disponibilités</span><br />
            <span class="small">Cliquez sur un jour ou une période pour cocher/décocher toute une ligne/colonne en une fois!</span>
            <table cellpadding="0" cellspacing="0" id="availabilities">
                <thead>
                    <tr>
                        <td>Jour</td>
                        <td onclick="availabilityDay_colOnClick( 'am' ); formOnChange( ); return false;">Matin</td>
                        <td onclick="availabilityDay_colOnClick( 'm' ); formOnChange( ); return false;">Midi</td>
                        <td onclick="availabilityDay_colOnClick( 'pm' ); formOnChange( ); return false;">Après-midi</td>
                        <td onclick="availabilityDay_colOnClick( 'e' ); formOnChange( ); return false;">Soir</td>
                    </tr>
                </thead>
                <!-- BEGIN availabilityDay -->
                <tr id="availabilityDayTr-{availabilityDay.dayIndex}">
                    <td class="infos{availabilityDay.tdClass}" onclick="availabilityDay_rowOnClick( 'availabilityDayTr-{availabilityDay.dayIndex}' ); formOnChange( ); return false;">{availabilityDay.dayLibelle}</td>
                    <td class="infosC{availabilityDay.tdClass}"><input type="checkbox" name="{availabilityDay.dayIndex}-am" id="{availabilityDay.dayIndex}-am" onclick="formOnChange( );" value="1" {availabilityDay.amChecked} /></td>
                    <td class="infosC{availabilityDay.tdClass}"><input type="checkbox" name="{availabilityDay.dayIndex}-m" id="{availabilityDay.dayIndex}-m" onclick="formOnChange( );" value="1" {availabilityDay.mChecked} /></td>
                    <td class="infosC{availabilityDay.tdClass}"><input type="checkbox" name="{availabilityDay.dayIndex}-pm" id="{availabilityDay.dayIndex}-pm" onclick="formOnChange( );" value="1" {availabilityDay.pmChecked} /></td>
                    <td class="infosC{availabilityDay.tdClass}"><input type="checkbox" name="{availabilityDay.dayIndex}-e" id="{availabilityDay.dayIndex}-e" onclick="formOnChange( );" value="1" {availabilityDay.eChecked} /></td>
                </tr>
                <!-- END availabilityDay -->
            </table>
            
        </div>
    </div>
    
    <div class="spacer">&nbsp;</div>
    
    
    <div class="infos0" style="width: 490px; float: left; margin: 20px 25px 10px 0px;">
        <span style="float: left; width: 90%;" class="subTitle" ondblclick="expandReduce('teaching'); return false;"><h3>Enseignement</h3></span>
        <span style="float: right; margin-top: 3px; margin-right: 3px;"><a href="#" onclick="expandReduce('teaching'); return false;"><img src="images/reduce.gif" id="teachingImg" /></a></span>
        <br /><br />
        
        <div style="float: left;" id="teaching">
            <span class="subtitle">Matière et niveau</span><br />
            
            <span id="teachingSubjects">
                <input type="checkbox" name="teachingLevel-0" value="0" {teachingLevel-0Checked}  onclick="formOnChange( );" /> Primaire et secondaire<br /><br />
                <input type="checkbox" name="teachingLevel-1" id="teachingLevel-1" value="1" id="teachingLevel-1" onclick="checkLevel( 1 ); formOnChange( );" {teachingLevel-1Checked} /> Gymnase<br />
                    <div style="position: relative; left: 30px;" id="teachingLevel-1Opts">
                    <!-- BEGIN teachingsubject1Opt -->
                    <input type="checkbox" name="teachingSubjects-{teachingsubject1Opt.value}" value="{teachingsubject1Opt.value}" {teachingsubject1Opt.checked} onclick="if( this.checked ) $('teachingLevel-1').checked = true;  formOnChange( );" /> {teachingsubject1Opt.caption}<br />
                    <!-- END teachingsubject1Opt -->
                </div><br />
                <input type="checkbox" name="teachingLevel-2" id="teachingLevel-2" value="2" id="teachingLevel-2"  onclick="checkLevel( 2 );  formOnChange( );" {teachingLevel-2Checked} /> Université / HES / ...<br />
                <div style="position: relative; left: 30px;" id="teachingLevel-2Opts">
                    <!-- BEGIN teachingsubject2Opt -->
                    <input type="checkbox" name="teachingSubjects-{teachingsubject2Opt.value}" value="{teachingsubject2Opt.value}" {teachingsubject2Opt.checked} onclick="if( this.checked ) $('teachingLevel-2').checked = true; formOnChange( );" /> {teachingsubject2Opt.caption}<br />
                    <!-- END teachingsubject2Opt -->
                </div>
            </span>
            
            <br />
            <span class="subtitle">Langue d'enseignement</span><br />
            <span id="languages">
            <!-- BEGIN languageOpt -->
            <input type="checkbox" name="language-{languageOpt.value}" onclick="formOnChange( );" value="{languageOpt.value}" {languageOpt.checked}> {languageOpt.caption}<br />
            <!-- END languageOpt -->
            </span>
        </div>

    </div>
    
    <div class="infos0" style="width: 490px; float: left; margin: 0px 25px 10px 0px;">
        <span style="float: left; width: 90%;" class="subTitle" ondblclick="expandReduce('rep'); return false;"><h3>Informations répétiteur</h3></span>
        <span style="float: right; margin-top: 3px; margin-right: 3px;"><a href="#" onclick="expandReduce('rep'); return false;"><img src="images/reduce.gif" id="repImg" /></a></span>
        <br /><br />
        
        <div style="float: left;" id="rep">
            <span class="subtitle">Section du répétiteur</span><br />
            <div id="section">
                <!-- BEGIN sectionOpt -->
                <input type="checkbox" name="section-{sectionOpt.value}" value="{sectionOpt.value}" onclick="formOnChange( );" {sectionOpt.checked}> {sectionOpt.caption}<br />
                <!-- END sectionOpt -->
            </div>
            <br /><br />
            
            <span class="subtitle">Niveau du répétiteur</span><br />
            <div id="levelStudies">
                <!-- BEGIN levelStudiesOpt -->
                <input type="checkbox" name="levelStudies-{levelStudiesOpt.value}" value="{levelStudiesOpt.value}" onclick="formOnChange( );" {levelStudiesOpt.checked}> {levelStudiesOpt.caption}<br />
                <!-- END levelStudiesOpt -->
            </div>
        </div>
    </div>
    
    <div style="clear: both;">&nbsp;</div>
</div>

<input type="submit" name="submit" value="Lancer la recherche" />

<div class="fixed" id="studentFixed">
    Semestre: <br />
    <span id="termInfo" style="margin-left: 10px;">{termInfo}</span><br /><br />
    
    Nombre de cours choisis: <br />
    <span id="subjectInfo" style="margin-left: 10px;">Choisissez au moins un cours</span><br /><br />
    
    Nombre de disponibilités choisies: <br />
    <span id="availabilitiesInfo" style="margin-left: 10px;">{availabilityCount}</span><br /><br />
    
    Langues d'enseignement: <br />
    <span id="languageInfo" style="margin-left: 10px;">{languagesChecked}</span><br /><br />
    
    <br />
    
    <!-- TODO: info de base -->
    <span style="font-weight: bold;"> Répétiteurs correspondant à vos critères: <span id="countInfos">0</span></span><br /><br />
    
    <input type="submit" name="submit" value="Lancer la recherche" /><br />
</div>

</form>
