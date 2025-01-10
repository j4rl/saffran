<?php  
// Settings for language, theme and some corporate strings
$language = "en";
$theme = "default";
$company = "Saffran";
// get url of the company from the global variable $_SERVER
$company_url = $_SERVER['HTTP_HOST'];
//get logo url from /assets/logo.png and add the full path with help of $company_url
$logo_url = "http://".$company_url."/assets/logo.png";


?>