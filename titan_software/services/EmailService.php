<?php

//class de service para envio do email, após finalizar o serviço
class EmailService {

    //public function de enviar email
    public function enviarServicoFinalizado($email, $nome, $servico, $comissao) {

        //assunto
        $assunto = 'Serviço finalizado';

        //monta o corpo do email
        $mensagem  = "Olá, {$nome}.\n\n";
        $mensagem .= "O serviço \"{$servico['description']}\" foi finalizado.\n";
        $mensagem .= "Valor: R$ " . number_format($servico['price'], 2, ',', '.') . "\n";
        $mensagem .= "Comissão: R$ " . number_format($comissao, 2, ',', '.') . "\n";
        $mensagem .= "Data de finalização: " . date('d/m/Y H:i');

        //configura os dados do envio

        //busca o email cadastrado do usuario logado, para usar como remetente.
        //o email que vai ser disparado é de quem criou o serviço
        $email_remetente = $_SESSION['gbl_email'];

        $headers  = "From: $email_remetente\r\n";
        $headers .= "Reply-To: $email_remetente\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        //envia o email
        return mail($email, $assunto, $mensagem, $headers);
    }
}