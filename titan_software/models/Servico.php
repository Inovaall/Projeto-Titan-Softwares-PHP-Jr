<?php
//class responsável pelos serviços
class Servico {
    //variavel privada para toda a class de serviço
    private $conn;

    //recebe a conexão com o banco
    public function __construct(PDO $conn) {$this->conn = $conn;}

    //lista os serviços, recebe parametros dentro da var $filtros e aplica eles na consulta
    public function listar($filtros = []) {
        //slq
        $sql = "
            SELECT s.id_service, s.description, s.price, s.created_at, s.finished_at, s.commission_user, 
            u.id_user, u.name, u.email,
                CASE
                    WHEN s.finished_at IS NULL THEN 'PENDENTE'
                    ELSE 'FINALIZADO'
                END AS status
            FROM service s
            INNER JOIN user u ON u.id_user = s.user_id_user
            WHERE s.id_service <> 0
        ";

        //guarda os parametros dos filtros
        $params = [];

        //filtro pelo nome do serviço
        if (!empty($filtros['nome'])) {
            $sql .= " AND s.description LIKE :nome";
            $params[':nome'] = '%' . $filtros['nome'] . '%';
        }

        //filtro pelo nome do usuario
        if (!empty($filtros['usuario'])) {
            $sql .= " AND u.name LIKE :usuario";
            $params[':usuario'] = '%' . $filtros['usuario'] . '%';
        }

        //filtro pela data inicial
        if (!empty($filtros['data_inicial'])) {
            $sql .= " AND DATE(s.created_at) >= :data_inicial";
            $params[':data_inicial'] = $filtros['data_inicial'];
        }

        //filtro pela data final
        if (!empty($filtros['data_final'])) {
            $sql .= " AND DATE(s.created_at) <= :data_final";
            $params[':data_final'] = $filtros['data_final'];
        }

        //filtro pelo status do serviço
        if (!empty($filtros['status'])) {
            if ($filtros['status'] === 'PENDENTE') {
                $sql .= " AND s.finished_at IS NULL";
            }

            if ($filtros['status'] === 'FINALIZADO') {
                $sql .= " AND s.finished_at IS NOT NULL";
            }
        }

        //ordena pelos serviços mais recentes
        $sql .= " ORDER BY s.id_service DESC";

        //prepara a consulta com statement
        $stmt = $this->conn->prepare($sql);

        //passa os valores dos filtros para a consulta
        foreach ($params as $campo => $valor) {
            $stmt->bindValue($campo, $valor);
        }

        //executa
        $stmt->execute();

        //retorna todos os serviços encontrados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //busca um serviço pelo id
    public function buscarPorId($id) {
        //slq da busca
        $sql = "
            SELECT s.*, u.name, u.email
            FROM service s
            INNER JOIN user u ON u.id_user = s.user_id_user
            WHERE s.id_service = :id LIMIT 1
        ";

        //prepara a consulta com statement
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        //retorna os dados do serviço
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //cadastra um novo serviço
    public function cadastrar($descricao, $valor, $usuarioId) {
        //sql de insert no bd
        $sql = "
            INSERT INTO service (description, price, created_at, commission_user, user_id_user) 
            VALUES ( :description, :price, NOW(), 0, :usuario_id)
        ";

        //statement
        $stmt = $this->conn->prepare($sql);

        //passa os dados para o cadastro
        $stmt->bindValue(':description', $descricao);
        $stmt->bindValue(':price', $valor);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);

        return $stmt->execute();
    }

    //atualiza os dados do serviço
    public function atualizar($id, $descricao, $valor) {
        //sql de update
        $sql = "
            UPDATE service SET description = :description, price = :price, update_at = NOW() WHERE id_service = :id
        ";

        //statement
        $stmt = $this->conn->prepare($sql);

        //passa os dados que serão atualizados
        $stmt->bindValue(':description', $descricao);
        $stmt->bindValue(':price', $valor);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    //exclui o serviço pelo id
    public function excluir($id) {
        //delete no bd (eu gostaria de implementar SOFT DELETE por questão de segurança e BK)
        $sql = "DELETE FROM service WHERE id_service = :id";

        //statement
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    //finaliza o serviço e grava a comissão
    public function finalizar($id, $comissao) {
        $sql = "
            UPDATE service SET finished_at = NOW(), commission_user = :comissao, update_at = NOW()
            WHERE id_service = :id AND finished_at IS NULL
        ";

        $stmt = $this->conn->prepare($sql);

        //passa a comissão e o id do serviço
        $stmt->bindValue(':comissao', $comissao);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    //soma o valor dos serviços do usuario
    public function totalPorUsuario($usuarioId) {
        $sql = "SELECT COALESCE(SUM(price), 0) FROM service WHERE user_id_user = :usuario_id AND finished_at IS NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        //retorna o valor total
        return $stmt->fetchColumn();
    }

    //soma o valor dos serviços finalizados do usuario
    public function totalPorUsuarioF($usuarioId) {
        $sql = "SELECT COALESCE(SUM(price), 0) FROM service WHERE user_id_user = :usuario_id AND finished_at IS NOT NULL";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        //retorna o valor total
        return $stmt->fetchColumn();
    }

    //busca os ultimos serviços pendentes do usuario
    public function pendentesPorUsuario($usuarioId) {
        $sql = "
            SELECT id_service, description, price, created_at
            FROM service WHERE user_id_user = :usuario_id AND finished_at IS NULL
            ORDER BY created_at DESC LIMIT 3
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();

        //retorna até 3 serviços pendentes
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}