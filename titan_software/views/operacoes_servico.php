<?php
//variável para verificar se existe um serviço carregado para edição
$editando = !empty($servico);

//define o título dinamicamente conforme cadastro ou edição
if ($editando) {
    $titulo = 'Alterar Serviço';
} else {
    $titulo = 'Cadastrar Serviço';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/titan_software/public/css/style.css">

    <!--título dinâmico para cadastro ou alteração-->
    <title><?= $titulo ?></title>
</head>

<body>
    <div class="container-login">
        <div class="card-login">
            <!--action dinâmico para cadastrar ou atualizar o serviço-->
            <form action="/titan_software/index.php?acao=<?= $editando ? 'atualizar_servico' : 'cadastrar_servico' ?>" method="post">
                <!-- Cabeçalho do formulário -->
                <div class="form-header">
                    <h1>
                        <?=$titulo?>
                    </h1>
                </div>

                <?php if (!empty($_GET['sucesso'])): ?>
                    <!--exibe mensagem de sucesso enviada pela url-->
                    <div class="alert-success">
                        <?=$_GET['sucesso']?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($_GET['erro'])): ?>
                    <!--exibe mensagem de erro enviada pela url-->
                    <div class="alert-error">
                        <?=$_GET['erro']?>
                    </div>
                <?php endif; ?>

                <?php if ($editando): ?>
                    <!--coloca o id do serviço em um hidden para atualização-->
                    <input type="hidden" name="id_service" value="<?= $servico['id_service'] ?>">
                <?php endif; ?>

                <!--input de descrição do serviço-->
                <div class="row">
                    <!--caso ja tenha registro, carrega o valor-->
                    <input type="text" class="input-form" name="description" maxlength="45" placeholder="descrição" value="<?= $editando ? $servico['description'] : '' ?>">
                </div>

                <!--campo para informar o valor do serviço, e se ja tiver valor, ele carrega-->
                <div class="row">
                    <input type="text" class="input-form valor" name="price" placeholder="R$ 0,00" value="<?= $editando ? $servico['price'] : '' ?>">
                </div>

                <div class="row row-acoes">
                    <!--btn para ação-->
                    <div class="row-e">
                        <button type="submit" class="btn-form"><?= $editando ? 'Atualizar' : 'Cadastrar' ?></button>
                    </div>

                    <!--redireciona para a pagina do dashboard-->
                    <div class="row-d">
                        <a href="/titan_software/index.php" class="a-form">Voltar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--import de scripts-->
    <script src="/titan_software/public/js/jquery-3.7.1.min.js"></script>
    <script src="/titan_software/public/js/script.js"></script>

    <?php if (!empty($_GET['redirecionar'])): ?>
        <script>
            //delay de 2,5 segundos antes de retornar ao dashboard
            setTimeout(function () {
                window.location.href = '/titan_software/index.php';
            }, 2500);
        </script>
    <?php endif; ?>
</body>
</html>