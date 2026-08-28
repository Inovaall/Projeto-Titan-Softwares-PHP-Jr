<?php
//arquivo responsável pela conexão com o banco de dados.

//class para conexão
class Conexao{
    //variaveis privadas de banco
    private $host= "localhost";
    private $dbname= "titan";
    private $user = "root";
    private $pass = "";
    private $conn;

    //função para conectar 
    public function conectar(){
        try{
            //cria a conexão com o banco
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8mb4", $this->user, $this->pass);

            //ativa os erros do pdo
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //retorna a conexão
            return $this->conn;

        }catch(PDOException $e){
            //exibe o erro caso a conexão falhe
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }
    }
}