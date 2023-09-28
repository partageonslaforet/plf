<?php


require __DIR__ . "/../Parameters.php";
//require __DIR__ . "/../functions.php";


$mydate = date("d-m-Y");

echo $mydate;


//goto Test1_new;
//goto Test2_new;
//goto Test3_new;
//goto Test4_new;
//goto Test5_new;
//goto Test6_new;
//goto Test7_new;
//goto Test8_new;
//goto Test12_new;
//goto Test13_new;
//goto Test14_new;
goto Test15_new;
//goto Test16_new;


exit;


Test1_new:

$List_Territoires = PLF::Get_Territoire_List("2023");

if ($List_Territoires[0] < 0) {

   echo $List_Territoires[1];

   //
   // .... traitement de l'erreur
   //
}







Test2_new:

$Territories_Info = PLF::Get_Territoire_Info("6113105040", "2023");

if ($Territories_Info[0] < 0) {

   echo $Territories_Info[1];

   //
   // .... traitement de l'erreur
   //
}




Test3_new:

$List_Chasse_Territories_By_Date = PLF::Get_Chasse_By_Date("6-10-2023", "2023");

if ($List_Chasse_Territories_By_Date[0] < 0) {

   echo $List_Chasse_Territories_By_Date[1];

   //
   // .... traitement de l'erreur
   //
}

if ($List_Chasse_Territories_By_Date[0] == -14) {

   echo "Pas de chasse pour cette date.";
}







Test4_new:


$List_Chasse_Dates_By_Territories = PLF::Get_Chasse_By_Territoire("7113184002", "2023");

if ($List_Chasse_Dates_By_Territories[0] < 0) {

   echo $List_Chasse_Dates_By_Territories[1];

   //
   // .... traitement de l'erreur
   //
}



if ($List_Chasse_Dates_By_Territories[0] == -15) {


   echo "Pas de date de chasse pour ce territoire.";
}





Test5_new:

$List_Cantons = PLF::Get_Canton_List();

if ($List_Cantons[0] < 0) {

   echo $List_Cantons[1];

   //
   // .... traitement de l'erreur
   //
}






Test6_new:

$List_Territoire_By_Canton = PLF::Get_Territoire_By_Canton("611", "2023");

if ($List_Territoire_By_Canton[0] < 0) {

   echo $List_Territoire_By_Canton[1];

   //
   // .... traitement de l'erreur
   //
}


if ($List_Territoire_By_Canton[0] == 0) {


   echo "Pas de Territoire pour ce canton.";
}







Test7_new:

$List_CC = PLF::Get_CC_List();

if ($List_CC[0] < 0) {

   echo $List_CC[1];

   //
   // .... traitement de l'erreur
   //
}



Test8_new:

$List_Territoire_By_CC = PLF::Get_Territoire_By_CC("CCGBCCV", "2023");

if ($List_Territoire_By_CC[0] < 0) {

   echo $List_Territoire_By_CC[1];

   //
   // .... traitement de l'erreur
   //
}






Test12_new:

$Territoire_Geometry = PLF::Territoire_JSON("6113105040", "2023");

if ($Territoire_Geometry[0] < 0) {

   echo $Territoire_Geometry[1];

   //
   // .... traitement de l'erreur
   //

} else {

   $fp = fopen("C:\Users\chris\OneDrive\\temp\Result_DA_Numero.json", 'w');
   fwrite($fp, $Territoire_Geometry[2]);
}


$Territoire_Geometry = PLF::Territoire_JSON("6113105046", "2023");

if ($Territoire_Geometry[0] < 0) {

   echo $Territoire_Geometry[1];

   //
   // .... traitement de l'erreur
   //

} else {

   $fp = fopen("C:\Users\chris\OneDrive\temp\Result_DA_Numero-2.json", 'w');
   fwrite($fp, $Territoire_Geometry[2]);
}


Test13_new:

$CC_Geometry = PLF::CC_JSON("CCFARM");

if ($CC_Geometry[0] < 0) {

   echo $CC_Geometry[1];

   //
   // .... traitement de l'erreur
   //

} else {

   $fp = fopen("C:\Users\chris\OneDrive\\temp\Result_CC.json", 'w');
   fwrite($fp, $CC_Geometry[2]);
}


Test14_new:

$Canton_Geometry = PLF::Canton_JSON("611");

if ($Canton_Geometry[0] < 0) {

   echo $Canton_Geometry[1];

   //
   // .... traitement de l'erreur
   //

} else {

   $fp = fopen("C:\Users\chris\OneDrive\\temp\Result_Canton.json", 'w');
   fwrite($fp, $Canton_Geometry[2]);
}




Test15_new:

$List_Itineraires = PLF::Get_Itineraires_List();

if ($List_Itineraires[0] < 0) {

   echo $List_Itineraires[1];

   //
   // .... traitement de l'erreur
   //
}







Test16_new:

$Itineraire_Info = PLF::Get_Itineraire_Infos(10);

if ($Itineraire_Info[0] < 0) {

   echo $Itineraire_Info[1];

   //
   // .... traitement de l'erreur
   //
}

Test17_new:

$lastRunTime = PLF::Get_LastRunTime();

if ($lastRunTime[0] < 0) {

   echo $lastRunTime[1];

   //
   // .... traitement de l'erreur
   //
}




End:

echo PHP_EOL . "Enf of process.";
