<?php
$cookie_name = "plf";
if(!isset($_COOKIE[$cookie_name])) {
  session_start();
  $file_suffix = session_id();
  setcookie($cookie_name,session_id(),time() + (86400 * 2), "/");  
} else { $file_suffix = $_COOKIE[$cookie_name];}
?>

<html>

<head>


</head>

 
<body>



<?php

$monfichier = "tototo" . $file_suffix . ".json";
echo "File suffix = " . $file_suffix . "<br>";
if(!isset($_COOKIE[$cookie_name])) {
  echo "Cookie named '" . $cookie_name . "' is not set!";
} else {
  echo "Cookie '" . $cookie_name . "' is set !<br>";
}
echo $monfichier;
?>



<?php
   echo "this is the third test"
?>

</body>


</html>




<?php

