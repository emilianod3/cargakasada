<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{$emaildata->subject}}</title>
</head>
<body style="padding-left: 30px; padding-top: 20px;">
    <p>{{$emaildata->dados}}</p>

    <p><a href="{{ $emaildata->urlsistema }}" style="background-color: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Clique aqui para Recuperar Acesso</a></p>

    <p style="font-size: 12px; color: gray;">
        Este e-mail foi gerado automaticamente. Por favor, <strong>não responda esta mensagem</strong>.<br>
    </p>
</body>