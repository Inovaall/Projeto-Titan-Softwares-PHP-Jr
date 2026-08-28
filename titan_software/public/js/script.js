//codigo js para msg
$(document).ready(function () {
    //mostra as mensagens com efeito
    $('.alert-success, .alert-error').hide().slideDown(400);

    //esconde a mensagem de erro e finalizado depois de 3 segundos e limpa url para nao aparcer mais
    setTimeout(function () {
        $('.alert-success, .alert-error').slideUp(400);

        //remove mensagem e erro da url
        const url = new URL(window.location.href);

        url.searchParams.delete('mensagem');
        url.searchParams.delete('erro');
        window.history.replaceState({}, document.title, url.pathname + url.search);

    }, 3000);

    //confirma antes de excluir o serviço
    $('.btn-excluir').on('click', function (e) {
        if (!confirm('Deseja realmente excluir este serviço?')) {
            e.preventDefault();
        }
    });

    //confirma antes de finalizar o serviço
    $('.btn-finalizar').on('click', function (e) {
        if (!confirm('Deseja realmente finalizar este serviço?')) {
            e.preventDefault();
        }
    });
});

//mascara valor em real para usar nos inputs de cadastro
$('.valor').on('input', function () {
    let valor = $(this).val().replace(/\D/g, '');

    valor = (valor / 100).toFixed(2);
    valor = valor.replace('.', ',');
    valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    $(this).val('R$ ' + valor);
});

//remove a mascara antes de enviar para o back-end e salvar no bd
$('form').on('submit', function () {
    $('.valor').each(function () {
        let valor = $(this).val();

        valor = valor.replace('R$', '');
        valor = valor.replace(/\./g, '');
        valor = valor.replace(',', '.');
        valor = valor.trim();

        $(this).val(valor);
    });
});

//formata os valores vindos do banco para mostrar na tela, inclusive os que vem do bd e mostra no input
$('.valor').each(function () {
    let valor = $(this).val();

    if (valor) {
        valor = parseFloat(valor).toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        });

        $(this).val(valor);
    }
});

//formata valores apenas para mostrar na tela
$('.valor-formatado').each(function () {
    let valor = parseFloat($(this).data('valor'));

    $(this).text(
        valor.toLocaleString('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        })
    );
});