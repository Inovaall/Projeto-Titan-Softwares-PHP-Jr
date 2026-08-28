<!--html da pagina de login-->
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!--configuração da página e arquivos de estilo-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="public/css/style.css">

    <!--titulo da página-->
    <title>Login</title>
</head>
<body>
    <div class="container-login">
        <div class="card-login">
            <form action="/titan_software/index.php?acao=login" method="post">
                <div class="form-header">
                    <!--titulo do sistema-->
                    <h1>Sistema de Controle de Serviços</h1>
                </div>

                <?php if (!empty($_GET['erro'])): ?>
                    <!--mensagem de erro-->
                    <div class="alert-error">
                        <?=$_GET['erro']?>
                    </div>

                <?php endif; ?>

                <!--campo de email-->
                <div class="row">
                    <label for="email">E-mail</label>
                    <input type="email" class="input-form" id="email" name="email" maxlength="100" placeholder="email@email.com">
                </div>

                <!--campo de senha-->
                <div class="row">
                    <label for="password">Senha</label>
                    <input type="password" class="input-form" id="password" name="password" maxlength="100" placeholder="*********">
                </div>

                <!--botões do formulário-->
                <div class="row row-acoes">
                    <div class="row-e">
                        <button type="submit" class="btn-form">Entrar</button>
                    </div>
                    <div class="row-d">
                        <a href="/titan_software/index.php?pagina=cadastro_usuario" class="a-form">Cadastrar Usuário</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--arquivos javascript-->
    <script src="/titan_software/public/js/jquery-3.7.1.min.js"></script>
    <script src="public/js/script.js"></script>
</body>
</html>