<?php
//arquivos usados no controller
require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/Usuario.php';

//controller responsável pelo login e cadastro
class AuthController {
    //faz o login do usuario buscando o email e caso encontre, compara os hash das senhas (a informada no input e a salva no bd)
    public function login() {
        //pega os dados enviados pelo formulario
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['password'] ?? '';

        //verifica se os campos foram preenchidos
        if (!$email || !$senha) {
            header('Location: /titan_software/index.php?erro=Preencha email e senha');
            exit;
        }

        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //busca o usuario pelo email
        $usuarioModel = new Usuario($conn);
        $usuario = $usuarioModel->buscarPorEmail($email);

        //verifica se o usuario existe e se a senha está correta
        if (!$usuario || !password_verify($senha, $usuario['password'])) {
            header('Location: /titan_software/index.php?erro=Ops, email ou senha inválido');
            exit;
        }

        //salva os dados do usuario na sessão
        $_SESSION['gbl_id']    = $usuario['id_user'];
        $_SESSION['gbl_nome']  = $usuario['name'];
        $_SESSION['gbl_email'] = $usuario['email'];

        //manda o usuario para o dashboard
        header('Location: /titan_software/index.php');
        exit;
    }

    //cadastra um novo usuario
    public function cadastrar() {
        //pega os dados enviados pelo formulario
        $nome  = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['password'] ?? '';

        //verifica se todos os campos foram preenchidos
        if (!$nome || !$email || !$senha) {
            header('Location: /titan_software/index.php?pagina=cadastro_usuario&erro=Preencha todos os campos');
            exit;
        }

        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //carrega o model de usuario
        $usuarioModel = new Usuario($conn);

        //verifica se o email já está cadastrado
        if ($usuarioModel->buscarPorEmail($email)) {
            header('Location: /titan_software/index.php?pagina=cadastro_usuario&erro=E-mail já cadastrado');
            exit;
        }

        //gera a senha criptografada (password_hash = hash padrão do PHP)
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        //salva o usuario no banco
        $usuarioModel->cadastrar($nome, $email, $senhaHash);

        //volta para o login com mensagem de sucesso
        header('Location: /titan_software/index.php?sucesso=Usuário cadastrado com sucesso');
        exit;
    }

    //encerra a sessão do usuario
    public function logout() {
        //destruir sessaão atual
        session_unset();
        session_destroy();

        //volta para a tela de login
        header('Location: /titan_software/index.php');
        exit;
    }
}