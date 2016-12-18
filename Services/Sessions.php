<?php
session_start();
    class Sessions{
        public function Session(){
            $sessionvalidationresult = false;
            if($_SESSION["_UserName"]){
                $sessionvalidationresult = true;
            }
            else {
                $header = header('location: ../'); 
            }
            return $sessionvalidationresult;
        }
    }

