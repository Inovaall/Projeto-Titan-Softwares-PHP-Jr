<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!--configurações básicas da página-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--estilo da página-->
    <link rel="stylesheet" href="/titan_software/public/css/style.css">

    <title>Cadastrar Usuário</title>
</head>

<body>
    <!--área principal do cadastro-->
    <div class="container-login">
        <div class="card-login">
            <!--envia os dados para o cadastro do usuário-->
            <form action="/titan_software/index.php?acao=cadastrar_usuario" method="post">
                <div class="form-header">
                    <h1>Cadastrar Novo Usuário</h1>
                </div>

                <?php if (!empty($_GET['erro'])): ?>
                    <!--mostra o erro caso o cadastro não seja feito-->
                    <div class="alert-error"><?=$_GET['erro']?></div>
                <?php endif; ?>

                <!--campo para o nome do usuário-->
                <div class="row">
                    <label for="nome">Nome</label>
                    <input type="text" class="input-form" id="nome" name="nome" maxlength="100" placeholder="Nome completo">
                </div>

                <!--campo para o email do usuário-->
                <div class="row">
                    <label for="email">E-mail</label>
                    <input type="email" class="input-form" id="email" name="email" maxlength="100" placeholder="email@email.com">
                </div>

                <!--campo para criar a senha-->
                <div class="row">
                    <label for="password">Senha</label>
                    <input type="password" class="input-form" id="password" name="password" maxlength="100" placeholder="********">
                </div>

                <!--botão para finalizar o cadastro-->
                <div class="row row-acoes">
                    <div class="row-e">
                        <button type="submit" class="btn-form">Cadastrar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--carrega os arquivos javascript-->
    <script src="/titan_software/public/js/jquery-3.7.1.min.js"></script>
    <script src="public/js/script.js"></script>
</body>
</html>