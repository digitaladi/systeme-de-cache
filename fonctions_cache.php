<style>
    table{
        margin: 0 auto;
    }
    table, td, th{
        border-collapse: collapse;
        border: 1px solid black;
    }

    td, th{
        width: 200px;
        height: 50px;
        text-align: center;
        font-family: sans-serif;
        padding: 5px;
    }
    th{
        background-color: #22ff0e;
    }

    tr:nth-child(odd) {
        background: silver;


</style>

<?php
//Permet de voir le temp de chagement d'une page
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}
$debut = getmicrotime();


define("BR", '<br/>');
//PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' :   Permet d'interpreter les caracteres spéciaux
try{
    $pdo = new PDO("mysql:host=localhost;dbname=cache", 'root', "", array(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    echo "Connexion à la base de données" . BR . BR;
}
catch(Exception $e){
    echo  "Connexion ratée " . $e->getMessage() . BR ;
}
/*
$requete = $pdo->prepare("INSERT INTO client (prenom, nom, ville, age)VALUES ('Harouna', 'tim', 'paris', 56)");
$requete->execute();
*/
$requete = $pdo->query("SELECT * FROM client");
$requete->execute();
//$all =$requete->fetchAll()
?>
<table >
    <tr>
        <th >Prenom</th>
        <th>Nom</th>
        <th >Ville</th>
        <th>Age</th>
    </tr>

    <?php

    //pour chaque enregistrement on le récupere sous forme d'objet

    //début de la mise en tampon . c'est à dire à partir de cette methode jusqu'à ob_get_clean() tout ce qu'il y'a ne sera pas afficher dans la page
    //ce contenu sera stocké dans $tampon
    ob_start();
    while ($data = $requete->fetchObject()){
        ?>
        <tr>
            <td> <?= $data->prenom ?></td>
            <td> <?= $data->nom ?></td>
            <td> <?= $data->ville ?></td>
            <td> <?= $data->age ?></td>
        </tr>



        <?php
    }
    //mise en fin du tampon
    //tout donnée qui se trouve entre les 2 fonctions va etre stocké dans le tampon ou buffer  $tampon
    //nos données de la base de données se trouve dans tampon
    $tampon = ob_get_clean();





    $fin = getmicrotime();
    echo "Page générée en ".round($fin-$debut, 3) ." secondes.<br />";

    ?>


