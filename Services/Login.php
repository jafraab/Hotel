<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once ('AccountManager.php');
    if(isset($_SESSION))
    {
        session_start();
        session_unset();
        session_destroy();
    }
    session_start();
    session_regenerate_id(true); 
    if(($_SERVER["REQUEST_METHOD"] == "POST")){
        $loginuser = trim($_POST['USUARIO']);
        $loginpass = trim($_POST['PASSWORD']);
        
        $accman = new AccountManager($loginuser, md5($loginpass));
        $loginresult = $accman->Login();
        if ($loginresult){
            $_SESSION['_UserName'] = $loginuser;
            $header = header('location: ../Pages/');             
        }
        else{
            $header = header('location: ../'); 
        }
    }
    else{
        header('location: ../Login.html');
    }

