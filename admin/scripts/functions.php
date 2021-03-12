<?php 
//provide a location, use the header which is how direct people in PHP
function redirect_to($location=null){
     if($location!= null){
         //echo 'about to rediect';
         header('Location: '.$location);//set a location to destination
         exit;
     }
}


