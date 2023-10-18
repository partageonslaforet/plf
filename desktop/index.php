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
        <link rel="stylesheet" href="css/fontawesome.css">
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
       
        <!-- FICHIERS JS -->
        <script src = "../assets/inc/js/script.js"></script>
        <script src = "../assets/inc/js/bootstrap.bundle.min.js"></script>
        <script src = "https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src = "https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.js"></script>
        
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
        <div class="container col-md-8 text-center rounded-3 mt-5">
            <div class="text-uppercase fw-bold text-danger d-flex justify-content-center"><h2>Soyez les bienvenus sur le site partageonslaforet.be</h2></div>
        </div>
        <div id="message" class="details my-5">
            <div class="container col-md-8 bg-white rounded-3 text-dark pb-4">
                <div class="row">
                    <div class="text-container flex-column d-flex justify-content-cente h-100 p-4">
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
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">INFORMATION GENERALE</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                    <p>
                        Bienvenue sur l'application permettant de localiser les territoires de chasse ayant obtenu une autorisation de fermeture des chemins en forêt les jours où des actions de chasse sont organisées <a class="affiche" href="../assets/img/Affiche Battues.jpg" id="affichesR" target="_blank">(affiches rouges).</a></br>
                        </p>
                        <p>
                        Elle permet également de localiser les territoires sur lesquels des titulaires du droit de chasse ont déclaré des actions de chasse, sans pour autant solliciter une autorisation de fermeture des chemins <a class="affiche" href="../assets/img/Affiche Annonce.jpg" id="affichesJ" target="_blank">(affiches jaunes).</a></br>
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
                    <center>
                        <a class="brochure" href="../assets/img/brochure_partageons_la_foret.pdf" download="brochure_partageons_la_foret.pdf">Télécharger la brochure "Comment entrer en foret en la respectant"</a>
                    </center>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </section> 
    <footer>   
        <div id="sponsors">
            <div class="container col-md-9">
                <div class="row mt-5">
                    <div class="col-3">
                        <img src="assets/img/Couleurs 2 verts vague.png" class="img-fluid max-width: 50% height: auto" alt="Logo PNHSFA">
                    </div>
                    <div class="col-3">
                        <img src="assets/img/00042517-WBT-Logo VISITWallonia.be - Vertical - Pantone 2995C - PNG.png" class="img-fluid max-width: 50% height: auto"  alt="Visit Wallonia.be">
                    </div>
                    <div lass="col-3">
                        <img src="assets/img/soutien_v_fr.png" class="img-fluid max-width: 50% height: auto"  alt="SPW">
                    </div>
                </div>
            </div>
        </div>
    </footer>

        <div id="popup">
            <div id="mailHeader">
                <span class="close" id="close-popup">&times;</span>
                <span class="title" id="popupTitle">ENVOYER UN MESSAGE</span>
            </div>
            <div id="infoEmail">Une question ? Une suggestion?</div>
            <form class="contact-form" action="assets/inc/php/contactform.php" method="post">
                    <div class="form-group input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" name="name" placeholder="Votre nom & prénom" Required>
                    </div>
                    <div class="form-group input-group">
                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                        <input type="text" class="form-control"name="mail" placeholder="Votre e-mail" required>
                    </div>
                    <div class="form-group input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="text" class="form-control"name="subject" placeholder="Sujet" required>
                    </div>
                    <div class="form-group input-group">
                        <span class="input-group-text"><i class="fas fa-comment-alt"></i></span>
                        <textarea name="message" class="form-control"placeholder="Votre message" rows="4" required></textarea>
                    </div>
                    <button id="envoiMail" type="submit" name="submit"><span id=" fas fa-paper-plane"></span>  ENVOYER</button>
            </form>
                <?php 
                $resultMailPos="";
                $resultMailNeg="";
                if(isset($_GET['success'])){
                    $resultMailPos="Votre message a été transmit";
                    echo "<script> alert ('$resultMailPos');</script>";
                    }
                ?>
        </div>
    </body>
    <script>
        
        jQuery(document).ready(function($){    
            let toggle = document.querySelector('.toggle');
            let menu = document.querySelector('.menu');
            menu.classList.toggle('active')
           
            toggle.onclick = function(){
                menu.classList.toggle('active')
                }
                
                
            const icon = document.getElementById('icon');
            const popup = document.getElementById('popup');
            const closePopup = document.getElementById('close-popup');    
                
                
            icon.addEventListener('click', function() {
                // Affichez le popup
                console.log("coucou");
                popup.style.display = 'block';
            });
            
            // Ajoutez un gestionnaire d'événement au clic sur le bouton de fermeture du popup
            closePopup.addEventListener('click', function() {
                // Masquez le popup
                popup.style.display = 'none';
            });    
                            
                
        
        })      
    </script>
        
</html>

      