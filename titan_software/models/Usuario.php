<?php
//class responsável pelos dados do usuario (logar e cadastrar novo)
class Usuario {
    //variavel privada da class
    private $conn;

    //recebe a conexão com o banco
    public function __construct(PDO $conn) {$this->conn = $conn;}

    //busca um usuario pelo email para login
    public function buscarPorEmail($email) {
        //select no bd
        $sql = "SELECT * FROM user WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($sql);

        //passa o email para a consulta
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        //retorna os dados do usuario
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //function para cadastrar um novo usuario
    public function cadastrar($nome, $email, $senha) {
        //sql de insert (add novo usuario)
        $sql  = "INSERT INTO user (name, email, password, created_at) VALUES (:nome, :email, :senha, NOW())";
        
        //prepara statement
        $stmt = $this->conn->prepare($sql);

        //passa os dados para o cadastro
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', $senha);

        //salva o usuario no banco (commit)
        return $stmt->execute();
    }
}