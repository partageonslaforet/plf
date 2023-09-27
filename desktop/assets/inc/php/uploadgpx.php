<?php
echo "coucou";
if(isset($_POST["submit"])) {
    $targetDir = "../../datas/uploadsgpx/"; // Directory where the uploaded file will be saved
    $targetFile = $targetDir . basename($_FILES["inputFile"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
}else{

   if(move_uploaded_file($_FILES["inputFile"]["tmp_name"], $targetFile)) {
            echo "The file ". basename( $_FILES["inputFile"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
}
?>