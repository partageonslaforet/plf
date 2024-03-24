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
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
       
        <!-- FICHIERS JS -->
        <script src = "https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src = "https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.js"></script>
        
    </header>
    <body>
        <div class="loader-wrapper">
            <span class="loader"><span class="loader-inner"></span></span> 
        </div>
    <script>
        $(window).on("load",function(){
            $(".loader-wrapper").fadeOut("slow");
        });   
    </script>
    
    <head class="site-header">
        
        <div class="logo-header">
            <div class="menu">
                <a class="toggle" href="calendrier.php"><i class="fa fa-calendar fa-2x" title="CALENDRIER DES BATTUES"></i></a>
                <li style="--i:0;">
                    <!--<a data-modal-target="#calendar"><i class="fa-solid fa-calendar-days" title="CALENDRIER"></i></a>-->
                    <a href="calendrier.php"><i class="fa fa-calendar" title="CALENDRIER DES BATTUES"></i></a>
                </li>
                <li style="--i:1;">
                    <a id="icon"><i class="fa fa-envelope" title="NOUS CONTACTER"></i></a>
                </li>
                <li style="--i:2;">
                    <a href="informations.php"class="disable"><i class="fa fa-info" title="INFORMATION"></i></a>
                </li>
                <li style="--i:3;">
                     <!--<a data-modal-target="#traceSidebar"><i class="fa fa-hiking" title="PARCOURS"></i></a>-->
                    <a href="parcours.php"><i class="fa fa-hiking" title="PARCOURS"></i></a>
                </li>
                <!--<li style="--i:3;">
                     <a data-modal-target="#territoriesSidebar"><i class="fa fa-location-dot" title="TERRITOIRES"></i></a>
                    <a href="https://partageonslaforet.be/territories.php" class="disabled"><i class="fa fa-location-dot" title="TERRITOIRES" ></i></a>
                </li>-->
                <li style="--i:4;">
                     <!--<a data-modal-target="#dnfSidebar"><i class="fa fa-tree" title="DNF"></i></a>-->
                    <a href="dnf.php"><i class="fa fa-tree title" title="INFORMATIONS DNF"></i></a>
                </li>
                <li style="--i:5;">
                    <!--<a ><i class="fas fa-user" title="LOGINS"></i></a>
                    <a href="http://partageonslaforet.be/insert_data.php"><i class="fa fa-user" title="LOGIN"></i></a>-->
                    <a href="cc.php"><i class="fa-solid fa-bullseye" title="INFORMATIONS CONSEILS CYNEGETIQUES"></i></a>
                </li>
                <li style="--i:6;">
                    <a href="trace.php"><i class="fa fa-location-arrow" title="CHARGER UN GPX<"></i></a>
                </li>
            </div>
        </div>
    </head>
    <body>

        <div id="title">
            <div id="siteName">Soyezezeee ole ole zles bienvenus sur le site partageonslaforet.be </div>
            <div id="slogan">Découvrez docker dddles dates de chasse de la saison 2023/2024 en Wallonie</div>
        </div>

        <div id="popupInfo">
            <p>Les informations communiquées sur ce site visent à améliorer la transparence sur les activités de chasse et n’ont qu’une valeur informative.
                Seules les affiches d'interdiction de circulation apposées aux entrées des bois ont valeur légale.
            </p>
            <a href="informations.php" id="btnLink"><button id="btnInfo">Plus d'informations</button></a>
        </div>
        
        <div id="sponsors">
            <img src="assets/img/Couleurs 2 verts vague.png" alt="Logo PNHSFA">
            <img src="assets/img/00042517-WBT-Logo VISITWallonia.be - Vertical - Pantone 2995C - PNG.png" alt="Visit Wallonia.be">
            <img src="assets/img/soutien_v_fr.png" alt="SPW">
        </div>
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

      