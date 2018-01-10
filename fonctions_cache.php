<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Systeme de mise en cache</title>
    <style>
        body{
            font-family: sans-serif;
            color: #444;
        }
        h1{
            background-color: green;
            color: white;
            padding: 10px;
            text-align: center;
            width: 400px;
        }
        h2{
            background-color: blue;
            color: white;
            padding: 10px;
            width: 400px;
        }

        i{
            background-color: yellow;
        }

        a:hover{
            background-color: #22ff0e;
        }

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
</head>
<body>
<h1>Système de mise en cache</h1>
<h3>Le principe est d'aller chercher les données dans un fichier cache au lieu de faire des aller retour à la base de données</h3>

<h2>Le déroulement du script de cache : </h2>

<ul>
    <li>Si le fichier de cache <i>"cache/cache.html"</i> n'existe pas  et si la différence entre le temps actuel <i>time()</i> et le temps de création du fichier <i>(filemtime("leCheminDuFichier"))</i>  est supérieur au durée de la vie(50) du cache </li>
    <li>alors on réecre le cache c'est à dire : </li>
    <li>On se connecte à la base de donnée</li>
    <li>On éxécute la requete</li>
    <li>avec la requete on récupere les données du client</li>
    <li>On va mettre les données dans un table html entre <i>ob_start</i> et <i>ob_get_clean</i> pour les stocker dans un tampon</li>
    <li>pour  stocker définitivement les données, on va mettre <i>ob_get_clean</i> dans le variable <i>$tampon</i> </li>
    <li>Maintenant nos données sont stockés dans le tampon $tampon  : <strong><i>$tampon = ob_get_clean();</i></strong></li>
    <li>cette tampon $tampon on le met dans le fichier <strong>cache/cache.html</strong>  : <strong><i>file_put_contents("cache/cache.html", $tampon);</i></strong></li>
    <li>On include le fichier cache () <strong><i>include "cache/cache.html"</i></strong></li>
    <li>//sinon si le fichier existe cache/cache.html et si la durée de vie du fichier est superieure à la différence entre le temps actuel time() et le temps  de la création du fichier (filemtime("leCheminDuFichier"))</li>
<li>//Au lieu d'aller chercher les données dans la bdd on va inclure le cache ou se trouve les données($tampon) fraichement ajputés; : <strong>include "cache/cache.html"; </strong></li>
</ul>



<?php
/*echo filemtime("cache/cache.html");
echo "<br>";
echo time();*/
//Permet de voir le temp de chagement d'une page
//C'est juste pour voir la différrence entre aller chercher directement les données dans le bdd ou aller les chercher dans le cache
function getmicrotime(){
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}



define("BR", '<br/>');
//PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' :   Permet d'interpreter les caracteres spéciaux


$debut = getmicrotime();

//dureé de vie de la cache
$cache_life = 50;





//time()(temps actuel)  : Retourne le timestamp UNIX actuel
//filemtime("leCheminDuFichier") : Lit la date à laquelle le fichier a été modifié pour la dernière fois.




//si le fichier le cache(cache/cache.html) n'existe pas  et ...
// tant que la différence entre le tant temps actuel et le temps de création du fichier est supérieur au durée de la vie du cache alors on réecre le cache
if(!file_exists("cache/cache.html") || (time() - filemtime("cache/cache.html") >= $cache_life)){


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


    ob_start();
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
        ?>
    </table>
    <?php
    //mise en fin du tampon
    //tout donnée qui se trouve entre les 2 fonctions va etre stocké dans le tampon ou buffer  $tampon
    //nos données de la base de données se trouve dans tampon
    $tampon = ob_get_clean();

    //On ajoute $tampon dans le fichier cache cache.html qui se trouve dans le dossier cache
    //dont tous les données de la requete vont etre stockes dans le fichier cache aller jeter un coup d'oeil, pour voir c'est magique
    file_put_contents("cache/cache.html", $tampon);

    //On va inclure  cache.html pour les afficher à nouveau
    include "cache/cache.html";

}
//sinon si le fichier existe cache/cache.html et si la durée de vie du fichier est superieure à la différence entre le temps actuel time() et le temps  de la création du fichier (filemtime("leCheminDuFichier"))
else{
    include "cache/cache.html";   //Au lieu d'aller chercher les données dans la bdd on va inclure le cache ou se trouve les données($tampon) fraichement ajputés
}










$fin = getmicrotime();
echo "Page générée en ".round($fin-$debut, 3) ." secondes.<br />";

?>


</body>
</html>