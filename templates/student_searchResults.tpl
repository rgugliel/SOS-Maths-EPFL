<h1>Résultats de la recherche</h1><br />

<span class="subtitle">Semestre de recherche:<br />{TERM_LIBELLE}</span><br /><br />

<!-- todo: dégager tout ce bordel -->
<style>
div#studentFixed
{
    position: fixed;
    top: 150px;
    
    padding: 2px;
    width: 420px;
    height: 450px;
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
    var year = {YEAR};
    var term = '{TERM}';
</script>

<div style="min-height: 280px;">
    Tous les répétiteurs affichés ci-dessous correspondent à vos disponibilités.<br />
        
    <table id="student_searchResults">
    	<thead>
    		<tr>
    			<td>Prénom et nom <a href="index.php?p=stu&searchId={SEARCH_ID}&order=name&orderD=ASC"><img src="images/up{SEARCH_ORDER_name_UP}.png" style="vertical-align: middle;" /></a><a href="index.php?p=stu&searchId={SEARCH_ID}&order=name&orderD=DESC"><img src="images/down{SEARCH_ORDER_name_DOWN}.png" style="vertical-align: middle;" /></a></td>
    			<td>Tarif / h <a href="index.php?p=stu&searchId={SEARCH_ID}&order=fee&orderD=ASC"><img src="images/up{SEARCH_ORDER_fee_UP}.png" style="vertical-align: middle;" /></a><a href="index.php?p=stu&searchId={SEARCH_ID}&order=fee&orderD=DESC"><img src="images/down{SEARCH_ORDER_fee_DOWN}.png" style="vertical-align: middle;" /></a></td>
    			<td>Section <a href="index.php?p=stu&searchId={SEARCH_ID}&order=section&orderD=ASC"><img src="images/up{SEARCH_ORDER_section_UP}.png" style="vertical-align: middle;" /></a><a href="index.php?p=stu&searchId={SEARCH_ID}&order=section&orderD=DESC"><img src="images/down{SEARCH_ORDER_section_DOWN}.png" style="vertical-align: middle;" /></a></td>
    			<td>Niveau <a href="index.php?p=stu&searchId={SEARCH_ID}&order=level&orderD=ASC"><img src="images/up{SEARCH_ORDER_level_UP}.png" style="vertical-align: middle;" /></a><a href="index.php?p=stu&searchId={SEARCH_ID}&order=level&orderD=DESC"><img src="images/down{SEARCH_ORDER_level_DOWN}.png" style="vertical-align: middle;" /></a></td>
    		</tr>
    	</thead>
    	
    	<!-- BEGIN row -->
    	<tr>
    		<td class="infos{row.NO}"><a href="#" onclick="repetiteurGetInfos( {row.ID} ); return false;">{row.NAME}</a></td>
    		<td class="infos{row.NO}">{row.FEES}</td>
    		<td class="infos{row.NO}" title="{row.SECTION_F}">{row.SECTION}</td>
    		<td class="infos{row.NO}" title="{row.LEVEL_F}">{row.LEVEL}</td>
    	</tr>
    	<!-- END row -->
    </table>
</div>

<div class="fixed" id="studentFixed">
    Cliquez sur le nom d'un répétiteur pour avoir des informations.
</div>