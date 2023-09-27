<?php
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";

$message_sent=false;
if (isset($_POST['mail']) && $_POST['mail'] != '') {
    $name = $_POST['name'];
    $subject = $_POST['subject'];
    $mailFrom = $_POST['mail'];
    $message = $_POST['message'];
    
    if(filter_var($mailFrom, FILTER_VALIDATE_EMAIL)) {
        $mailTo = "info@partageonslaforet.be";
        $headers = "de: ".$mailFrom;
        $headers .= "Répondre à : ".$mailFrom."r\n";
        $txt = "Vous avez recu un e-mail de ".$name." - ".$mailFrom."\n\n".$message;
        
        mail($mailTo, $subject, $txt, $headers);
        header("location:../../../index.php?success");
        $message_sent=true;
        $resultMailPos="Votre message a été transmit";
    }
    else {
        //$resultMailNeg="Votre message n'a pas été envoyé, veuillez réessayer";
        header("location:../../index.php?error");
        (die);
    }
}
else {
    //$resultMailNeg="Votre message n'a pas été envoyé, veuillez réessayer";
    header("location:../../index.php?error");
    (die);
}
?>