<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    require_once ('Db.php');
    class AccountManager{
        protected $user;
        protected $password;
        public function __construct($usuario, $clave) {
            $this->user = $usuario;
            $this->password = $clave;
        }
        public function Login(){
            $loginsucces = false;
            $db = new Db();
            
            $dataresult = $db->ExecQuery("SELECT LOGIN FROM sec_users WHERE LOGIN = '".$this->user."' AND PASSWORD = '".$this->password."'");
            if(mysqli_fetch_array($dataresult))
			{
				$loginsucces = true;
			}
			return $loginsucces;
        }
    }

