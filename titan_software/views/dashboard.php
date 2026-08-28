<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!--configuração da página e arquivos de estilo-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--arquivo de estilo-->
    <link rel="stylesheet" href="/titan_software/public/css/style.css">

    <!--titulo da página-->
    <title>Dashboard</title>
</head>

<body>
    <!--estrutura principal do dashboard-->
    <div class="dashboard">
        <!--menu lateral-->
        <aside class="sidebar">
            <!--dados do usuario logado-->
            <div class="usuario-logado">
                <span>Logado como:</span>
                <strong><?=$_SESSION['gbl_nome']?></strong>
                <span>Data:</span>
                <strong><?= date('d/m/Y') ?></strong>
            </div>

            <!--link para cadastrar serviço-->
            <a href="/titan_software/index.php?pagina=operacoes_servico" class="menu-link">Cadastrar Serviço</a>

            <!--link para sair do sistema-->
            <a href="/titan_software/index.php?acao=logout" class="menu-link">Sair</a>
        </aside>

        <!--conteudo do dashboard-->
        <main class="dashboard-content">
            <h1>DASHBOARD</h1>

            <?php if (!empty($_GET['mensagem'])): ?>
                <!--mensagem de sucesso-->
                <div class="alert-success"><?=$_GET['mensagem']?></div>
            <?php endif; ?>

            <?php if (!empty($_GET['erro'])): ?>
                <!--mensagem de erro-->
                <div class="alert-error"><?=$_GET['erro']?></div>
            <?php endif; ?>

            <!--total dos serviços PENDENTES do usuario-->
            <div class="total-servicos">
                <span>Total dos seus serviços pendentes</span>
                <strong class="valor-formatado" data-valor="<?=$totalUsuario?>">
                    <?=$totalUsuario?>
                </strong>
            </div>

            <!--total dos serviços PENDENTES do usuario-->
            <div class="total-servicos">
                <span>Total dos seus serviços finalizados</span>
                <strong class="valor-formatado" data-valor="<?=$totalUsuarioF?>">
                    <?=$totalUsuarioF?>
                </strong>
            </div>

            <!--resumo dos serviços-->
            <div class="dashboard-top">
                <!--ultimos serviços-->
                <div class="dashboard-box">
                    <h2>Últimos Serviços</h2>

                    <?php if (!empty($servicos)): ?>
                        <?php foreach (array_slice($servicos, 0, 3) as $servico): ?>
                            <!--dados do serviço-->
                            <p>
                                <?= $servico['id_service'] ?>
                                -
                                <?=$servico['description']?>
                            </p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!--mensagem caso não tenha serviços-->
                        <p>Nenhum serviço cadastrado.</p>
                    <?php endif; ?>
                </div>

                <!--serviços pendentes-->
                <div class="dashboard-box">
                    <h2>Serviços Pendentes</h2>

                    <?php if (!empty($pendentes)): ?>
                        <?php foreach ($pendentes as $servico): ?>
                            <!--dados do serviço pendente-->
                            <p>
                                <?= $servico['id_service'] ?>
                                -
                                <?=$servico['description']?>
                            </p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!--mensagem caso não tenha pendencias-->
                        <p>Nenhum serviço pendente.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!--form dos filtros da pagina-->
            <form method="get" action="/titan_software/index.php" class="filtros">
                <!--filtro por nome do serviço-->
                <input type="text" name="nome" placeholder="Nome do serviço" value="<?=$_GET['nome'] ?? '' ?>">

                <!--filtro por data inicial-->
                <input type="date" name="data_inicial" value="<?=$_GET['data_inicial'] ?? ''?>">

                <!--filtro por data final-->
                <input type="date" name="data_final" value="<?=$_GET['data_final'] ?? ''?>">

                <!--filtro por status-->
                <select name="status">
                    <option value="">Todos Status</option>
                    <option value="PENDENTE"
                        <?= ($_GET['status'] ?? '') === 'PENDENTE' ? 'selected' : '' ?>>
                        Pendente
                    </option>

                    <option value="FINALIZADO"
                        <?= ($_GET['status'] ?? '') === 'FINALIZADO' ? 'selected' : '' ?>>
                        Finalizado
                    </option>
                </select>

                <!--filtro por usuario-->
                <input type="text" name="usuario" placeholder="Nome do usuário" value="<?=$_GET['usuario'] ?? ''?>">

                <!--filtrar-->
                <button type="submit">Filtrar</button>

                <!--limpar filtros-->
                <a href="/titan_software/index.php" class="btn-limpar">Limpar</a>
            </form>

            <!--tabela de serviços-->
            <div class="tabela-servicos">
                <table>
                    <!--cabeçalho da tabela-->
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Status</th>
                            <th>Usuário</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <!--dados da tabela-->
                    <tbody>
                        <?php if (!empty($servicos)): ?>
                            <?php foreach ($servicos as $servico): ?>
                                <!--linha do serviço-->
                                <tr>
                                    <td><?= $servico['id_service'] ?></td>
                                    <td><?=$servico['description']?></td>
                                    <td class="valor-formatado" data-valor="<?=$servico['price']?>"><?=$servico['price']?></td>
                                    <td><?= $servico['status'] ?></td>
                                    <td><?=$servico['name']?></td>

                                    <!--ações do serviço-->
                                    <td class="acoes">
                                        <!--alterar serviço-->
                                        <a href="/titan_software/index.php?pagina=operacoes_servico&id=<?= $servico['id_service'] ?>">
                                            Alterar
                                        </a>

                                        <!--excluir serviço-->
                                        <a href="/titan_software/index.php?acao=excluir_servico&id=<?= $servico['id_service'] ?>" class="btn-excluir">
                                            Excluir
                                        </a>

                                        <?php if ($servico['status'] === 'PENDENTE'): ?>
                                            <!--finalizar serviço-->
                                            <a href="/titan_software/index.php?acao=finalizar_servico&id=<?= $servico['id_service'] ?>" class="btn-finalizar">
                                                Finalizar
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!--mensagem caso não tenha resultados-->
                            <tr>
                                <td colspan="6">Nenhum serviço encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!--arquivos javascript-->
    <script src="/titan_software/public/js/jquery-3.7.1.min.js"></script>
    <script src="public/js/script.js"></script>
</body>
</html>