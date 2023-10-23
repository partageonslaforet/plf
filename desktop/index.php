<?php
$cookie_name = "plf";
if(!isset($_COOKIE[$cookie_name])) {
  session_start();
  $file_suffix = session_id();
  setcookie($cookie_name,session_id(),time() + (86400 * 2), "/");  
} else { $file_suffix = $_COOKIE[$cookie_name];}

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $mailFrom = $_POST['mail'];
    $message = $_POST['message'];
    
    $mailTo = "olivier@conrard.be";
    $headers = "de: ".$mailFrom;
    $txt = "Vous avez reçu un e-mail de".$name.".\n\n".$message;
    
    mail($mailTo, $subject, $txt, $headers);
    header("Location:index.php?mailsend");
}

// ceci ests un commentaire

?>

<!DOCTYPE html>
<html>
    <header>
        
        <title>home</title>
        <html lang="fr">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <!-- FICHIERS CSS -->
        <link rel="stylesheet" href="assets/css/index.css">
        <link rel="stylesheet" href="assets/css/header.css">
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="assets/src/css/lightbox.css">
        <link rel="stylesheet" href="assets/src/css/jquery-ui.css">
        <link rel="stylesheet" href="css/fontawesome.css">
    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">    
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
       
        <!-- FICHIERS JS -->
        <script src = "../assets/inc/js/script.js"></script>
        <script src = "../assets/inc/js/bootstrap.bundle.min.js"></script>
        <script src = "../assets/src/js/lightbox.js"></script>
        <script src = "../assets/src/js/jquery-ui.js"></script>
        <script src = "https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src = "https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.js"></script>
        <script src = "https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
        <script src = "https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    </header>
    <body>
        <div class="loader-wrapper">
            <span class="loader"><span class="loader-inner"></span></span> 
        </div>
        <header class="header">
        <!--<head class="site-header">-->
        <div class="container-xl">
            <div class="row">
                <div class="col-md-12">
                    <div class="logo-header">
                        <div class="menu">
                            <a class="toggle" href="calendrier.php"><i class="fa fa-calendar fa-2x" title="CALENDRIER DES BATTUES"></i></a>
                            <li style="--i:0;">
                                <a href="calendrier.php"><i class="fa fa-calendar" title="CALENDRIER DES BATTUES"></i></a>
                            </li>
                            <li style="--i:1;">
                                <a id="icon"><i class="fa fa-envelope" title="NOUS CONTACTER"></i></a>
                            </li>
                            <li style="--i:2;">
                                <a href="informations.php"><i class="fa fa-info" title="INFORMATION"></i></a>
                            </li>
                            <li style="--i:3;">
                                <a href="parcours.php"><i class="fa fa-hiking" title="PARCOURS"></i></a>
                            </li>
                            <li style="--i:4;">
                                <a href="#territories.php"><i class="fa fa-location-dot" title="TERRITOIRES"></i></a>
                            </li>
                            <li style="--i:5;">
                                <a href="dnf.php"><i class="fa fa-tree title" title="INFORMATIONS DNF"></i></a>
                            </li>
                            <li style="--i:6;">
                                <a href="cc.php"><i class="fa-solid fa-bullseye" title="INFORMATIONS CONSEILS CYNEGETIQUES"></i></a>
                            </li>
                            <li style="--i:7;">
                                <a href="trace.php"><i class="fa fa-location-arrow" title="CHARGER UN GPX<"></i></a>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
        </head>
        <body>
        <section id="title" class=""  >
            <div class="container col-md-8 text-center rounded-3 mt-3">
                <div class="text-uppercase fw-bold text-danger d-flex justify-content-center"><h1>Soyez les bienvenus sur le site partageonslaforet.be</h1></div>
            </div>
            <div id="message" class="details my-5">
                <div class="container col-md-8 bg-white rounded-3 text-dark pb-4" style="--bs-bg-opacity: .6;">
                    <div class="row">
                        <div class="text-container flex-column d-flex justify-content-center h-100 p-4">
                            <h5>Les informations communiquées sur ce site visent à améliorer la transparence sur les activités de chasse et n’ont qu’une valeur  informative.
                                Seules les affiches d'interdiction de circulation apposées aux entrées des bois ont valeur légale.
                            </h5>
                        </div>
                    </div>
                
                    <!-- Button trigger modal -->
                    <button type="button" id="btnInfo" class="text-container d-flex justify-content-center " data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Plus d'informations
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-uppercase fw-bold text-danger mx-auto d-flex justify-content-center" id="exampleModalLabel">INFORMATION GENERALE</h5>
                            <button type="button" class="btn-close text-danger" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        <div class="text-container text-secondary d-flex justify-content-center fw-bold ">
                            <a class="brochure text-secondary d-flex justify-content-center" href="../assets/img/brochure_partageons_la_foret.pdf" download="brochure_partageons_la_foret.pdf">Télécharger la brochure : "Comment entrer en foret en la respectant"</a>
                        </div>
                    
                        <p>
                            Bienvenue sur l'application permettant de localiser les territoires de chasse ayant obtenu une autorisation de fermeture des chemins en forêt les jours où des actions de chasse sont organisées 
                            <a class="affiche text-danger w-100" href="../assets/img/Affiche Battues.jpg" data-lightbox="affiches-bois" id="affichesR"  target="_blank"  data-title="Affiche Rouge">
                            <!--<img src="../assets/img/Affiche Battues.jpg" class="img-fluid rounded-3">-->
                            (affiches rouges)
                            </a></br>
                            </p>
                            <p>
                            Elle permet également de localiser les territoires sur lesquels des titulaires du droit de chasse ont déclaré des actions de chasse, sans pour autant solliciter une autorisation de fermeture des chemins 
                            <a class="affiche text-warning w-100" href="../assets/img/Affiche Annonce.jpg" data-lightbox="affiches-bois" id="affichesJ" target="_blank" data-title="Affiche Jaune   ">
                            <!--<img src="../assets/img/Affiche Annonce.jpg" class="img-fluid rounded-3">-->
                            (affiches jaunes).</a></br>
                            </p>
                            <p>
                            Les informations communiquées sur ce site visent à améliorer la transparence sur les activités de chasse et n’ont qu’une valeur informative. Seules les affiches d'interdiction de circulation apposées aux entrées des bois ont valeur légale.</br> 
                            </p>
                            <p>
                            En effet, plusieurs points d’attention sont à prendre en considération :</br>
                            •	Les limites des territoires de chasse sur cette carte interactive sont celles qui ont été communiquées à l'administration par les conseils cynégétiques ou les titulaires de droit de chasse eux-mêmes. Elles ne sont pas toutes nécessairement d’une grande précision et parfaitement à jour. En conséquence et à titre d’exemple, un chemin/sentier en périphérie du territoire peut également être fermé alors qu’il apparaît en dehors du périmètre du territoire sur la carte. Cette donnée sera chaque année améliorée dans sa qualité. </br>
                            •	Précisons également que les titulaires n'ont pas nécessairement le droit de chasse sur l'entièreté de la surface comprise à l'intérieur de ces limites (exemple : les zones habitées).</br> 
                            •	Dans l'état actuel de la réglementation, des actions de chasse peuvent être organisées sans que le titulaire du droit de chasse en informe l’administration ou sans qu’il ne demande la fermeture des chemins. L’application vous présente les territoires chassés (avec ou sans fermeture des chemins) dont l’administration a connaissance.</br>
                            •	Les informations communiquées sur ce site sont normalement mises à jour quotidiennement. </br>
                            </p>
                            <p>
                            La responsabilité du SPW ne peut être invoquée du fait que les informations communiquées sur ce site seraient inexactes en ce que ces dernières ont une valeur purement informative.</br>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </section> 
        <footer>   
            <div class="container position-absolute " id="sponsors" style= "bottom: 5%;">
                <div class="col-8 row bg-white rounded-3 mx-auto vertical-center p-2" style="--bs-bg-opacity: .6;">
                    <div class="col-sm-4 col-12 d-flex align-items-center justify-content-center vh-10" >
                        <img src="assets/img/Couleurs 2 verts vague.png" class="img-fluid" alt="Logo PNHSFA">  
                    </div>
                    <div class="col-sm-4 col-12 d-flex align-items-center justify-content-center vh-10">
                        <img src="assets/img/00042517-WBT-Logo VISITWallonia.be - Vertical - Pantone 2995C - PNG.png" class="img-fluid"  alt="Visit Wallonia.be">
                    </div>
                    <div class="col-sm-4 col-12 d-flex align-items-center justify-content-center vh-10">
                        <img src="assets/img/soutien_v_fr.png" class="img-fluid" alt="SPW">
                    </div>
                </div>
            </div>
        </footer>
        <?php 
        $resultMailPos="";
        $resultMailNeg="";
        if(isset($_GET['success'])){
            $resultMailPos="Votre message a été transmit";
            echo "<script> alert ('$resultMailPos');</script>";
            }
        ?>
    
    </body>
    <script>

        lightbox.init()        
  
    </script>
        
</html>

      