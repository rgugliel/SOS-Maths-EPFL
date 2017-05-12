<h1>Page personnelle</h1>

<br /><br />

<div id="featured-content">
    <div id="column1">
        <h2>Informations personnelles</h2>
        
        <a href="index.php?p=rep&do=uptPsw">Changer son mot de passe</a><br /><br />
        <a href="index.php?p=rep&do=uptProfil">Modifier son profil</a>
    </div>
    <div id="column2">
        <h2>Paramètres des cours</h2>
        
        <p>
            <h3>Semestre courant</h3> <br />
            {termCurLib}<br />
            <a href="index.php?p=rep&do=term&term={termCur}">{termCurAction}</a>
            <br /><br />
            
            <h3>Prochain semestre</h3> <br />
            {termNextLib}<br />
            <a href="index.php?p=rep&do=term&term={termNext}">{termNextAction}</a>
            <br /><br />
        </p>
    </div>
</div>
