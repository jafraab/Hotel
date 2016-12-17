<?php
    require_once ('AppApi.php');
    class Db{
        //protected $host="192.168.1.100";
        protected $host="localhost";
        protected $port=3306;
        protected $socket="";
        protected $user="hotel";
        //protected $password="hot@2016";
        protected $password="hot@2016";
        protected $dbname="hotel";
        protected $connection;
        
        function __construct(){
            $this->GetConnection();
        }
        protected function GetConnection(){
            $this->connection = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
            if($this->connection->connect_errno)
			{
				die("Se produjo un error al intentar la conexión con la base de datos: (".$this->CONNECTION->connect_errno.")");
			}
            return $this->connection;
        }
        protected function CloseConnection(){
            $this->connection->close();
        }
        public function ExecQuery($qry)
        {
            $oQuery = $this->connection->query($qry);
            $this->CloseConnection();
            return $oQuery;
        }
        public function ExecProc($qry)
        {
            $result = mysqli_query($this->connection,$qry) or die("No pudo ejecutarse la rutina: " . mysqli_error($this->connection));
            $this->CloseConnection();
            return $result;
        }
        public function ExecProcParam($qry, $param)
        {
            $result = mysqli_query($this->connection,$qry) or die("No pudo ejecutarse la rutina: " . mysqli_error($this->connection));
            $resultparam = '';
            if($result){
                $returnedval = mysqli_query($this->connection,"Select ".$param);
                $num_r=$returnedval->num_rows;
                if($num_r > 0){
                    $dr = $returnedval->fetch_object();
                    $resultparam = $dr->{$param};
                }
            }
            $this->CloseConnection();
            return $resultparam;
        }
                
    }