<?php
//arquivos usados no controller
require_once __DIR__ . '/../config/conn.php';
require_once __DIR__ . '/../models/Servico.php';
require_once __DIR__ . '/../services/EmailService.php';

//controller responsável pelos serviços
class ServicoController {
    //lista os serviços usando os filtros da tela
    public function listar() {
        //pega os filtros enviados pela url
        $filtros = [
            'nome'         => trim($_GET['nome'] ?? ''),
            'usuario'      => trim($_GET['usuario'] ?? ''),
            'data_inicial' => $_GET['data_inicial'] ?? '',
            'data_final'   => $_GET['data_final'] ?? '',
            'status'       => $_GET['status'] ?? ''
        ];

        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //busca os serviços
        $servicoModel = new Servico($conn);
        return $servicoModel->listar($filtros);
    }

    //cadastra um novo serviço
    public function cadastrar() {
        //pega os dados enviados pelo formulario
        $descricao = trim($_POST['description'] ?? '');
        $valor     = $_POST['price'] ?? '';
        $usuarioId = $_SESSION['gbl_id'] ?? null;

        //verifica se todos os dados foram preenchidos
        if (!$descricao || !$valor || !$usuarioId) {
            header('Location: /titan_software/index.php?pagina=operacoes_servico&erro=Preencha todos os campos');
            exit;
        }

        //verifica se o valor informado é valido
        if (!is_numeric($valor) || $valor <= 0) {
            header('Location: /titan_software/index.php?pagina=operacoes_servico&erro=Valor do serviço inválido');
            exit;
        }

        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //carrega o model de serviço
        $servicoModel = new Servico($conn);

        try {
            //tenta cadastrar o serviço
            if (!$servicoModel->cadastrar($descricao, $valor, $usuarioId)) {
                header('Location: /titan_software/index.php?pagina=operacoes_servico&erro=Não foi possível cadastrar o serviço');
                exit;
            }

            //volta para a tela com mensagem de sucesso
            header('Location: /titan_software/index.php?pagina=operacoes_servico&sucesso=Serviço cadastrado com sucesso&redirecionar=1');
            exit;

        } catch (PDOException $e) {
            //volta para a tela caso aconteça algum erro
            header('Location: /titan_software/index.php?pagina=operacoes_servico&erro=Não foi possível cadastrar o serviço');
            exit;
        }
    }

    //busca um serviço pelo id
    public function buscarPorId($id) {
        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //busca os dados do serviço
        $servicoModel = new Servico($conn);
        return $servicoModel->buscarPorId($id);
    }

    //atualiza um serviço
    public function atualizar() {
        //pega os dados enviados pelo formulario
        $id = (int) ($_POST['id_service'] ?? 0);
        $descricao = trim($_POST['description'] ?? '');
        $valor = $_POST['price'] ?? '';

        //verifica se os dados foram enviados corretamente
        if (!$id || !$descricao || !$valor) {
            header('Location: /titan_software/index.php?erro=Não foi possível atualizar o serviço');
            exit;
        }

        //verifica se o valor informado é valido
        if (!is_numeric($valor) || $valor <= 0) {
            header('Location: /titan_software/index.php?pagina=operacoes_servico&id=' . $id . '&erro=Valor do serviço inválido');
            exit;
        }

        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //carrega o model de serviço
        $servicoModel = new Servico($conn);

        try {
            //atualiza os dados do serviço
            $servicoModel->atualizar($id, $descricao, $valor);

            header('Location: /titan_software/index.php?mensagem=Serviço atualizado com sucesso');
            exit;

        } catch (PDOException $e) {
            //volta para o dashboard caso aconteça algum erro
            header('Location: /titan_software/index.php?erro=Não foi possível atualizar o serviço');
            exit;
        }
    }

    //exclui um serviço
    public function excluir() {
        //pega o id enviado pela url
        $id = (int) ($_GET['id'] ?? 0);

        //verifica se o id é valido
        if (!$id) {
            header('Location: /titan_software/index.php?erro=Serviço inválido');
            exit;
        }

        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //carrega o model de serviço
        $servicoModel = new Servico($conn);

        try {
            //exclui o serviço
            $servicoModel->excluir($id);

            header('Location: /titan_software/index.php?mensagem=Serviço excluído com sucesso');
            exit;

        } catch (PDOException $e) {
            //volta para o dashboard caso aconteça algum erro
            header('Location: /titan_software/index.php?erro=Não foi possível excluir o serviço');
            exit;
        }
    }

    //finaliza um serviço
    public function finalizar() {
        //pega o id enviado pela url
        $id = (int) ($_GET['id'] ?? 0);

        //verifica se o id é valido
        if (!$id) {
            header('Location: /titan_software/index.php?erro=Serviço inválido');
            exit;
        }

        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //busca o serviço pelo id
        $servicoModel = new Servico($conn);
        $servico = $servicoModel->buscarPorId($id);

        //verifica se o serviço existe
        if (!$servico) {
            header('Location: /titan_software/index.php?erro=Serviço não encontrado');
            exit;
        }

        //impede finalizar o mesmo serviço novamente
        if (!empty($servico['finished_at'])) {
            header('Location: /titan_software/index.php?erro=Serviço já finalizado');
            exit;
        }

        //pega o valor do serviço
        $valor = (float) $servico['price'];

        //calcula a comissão de acordo com o valor
        if ($valor <= 1000) {
            //Para valores abaixo ou igual a R$ 1.000,00 será dado 5% de comissão
            $comissao = $valor * 0.05;
        } elseif ($valor <= 10000) {
            //Para valores acima de R$ 1.000,00 será dado 10% de comissão
            $comissao = $valor * 0.10;
        } else {
            //Para valores acima de R$ 10.000,00 será dado 20% de comissão.
            $comissao = $valor * 0.20;
        }

        try {
            //salva a finalização e a comissão
            if (!$servicoModel->finalizar($id, $comissao)) {
                header('Location: /titan_software/index.php?erro=Não foi possível finalizar o serviço');
                exit;
            }

            //prepara o envio do email
            $emailService = new EmailService();

            //envia o email para o usuario do serviço
            $emailEnviado = $emailService->enviarServicoFinalizado($servico['email'], $servico['name'], $servico, $comissao);

            //avisa caso o email não seja enviado
            if (!$emailEnviado) {
                header('Location: /titan_software/index.php?erro=Serviço finalizado, mas não foi possível enviar o email');
                exit;
            }

            //volta para o dashboard com sucesso
            header('Location: /titan_software/index.php?mensagem=Serviço finalizado com sucesso');
            exit;

        } catch (PDOException $e) {
            //volta para o dashboard caso aconteça algum erro
            header('Location: /titan_software/index.php?erro=Não foi possível finalizar o serviço');
            exit;
        }
    }

    //busca o valor total dos serviços do usuario
    public function totalPorUsuario($usuarioId) {
        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //retorna o valor total
        $servicoModel = new Servico($conn);
        return $servicoModel->totalPorUsuario($usuarioId);
    }

    //busca o valor total dos serviços pendentes do usuario
    public function totalFinalizadoPorUsuario($usuarioId) {
        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //retorna o valor total
        $servicoModel = new Servico($conn);
        return $servicoModel->totalPorUsuarioF($usuarioId);
    }

    //busca os serviços pendentes do usuario
    public function pendentesPorUsuario($usuarioId) {
        //abre a conexão com o banco
        $conexao = new Conexao();
        $conn = $conexao->conectar();

        //retorna os serviços pendentes
        $servicoModel = new Servico($conn);
        return $servicoModel->pendentesPorUsuario($usuarioId);
    }
}