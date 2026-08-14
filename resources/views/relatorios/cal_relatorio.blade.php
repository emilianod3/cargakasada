<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $titulo1 }}</title> 
    <style>
        body { font-family: sans-serif; }

        .table-report { 
            width: 100%; 
            border-collapse: collapse; /* Crucial: garante que as bordas das células se unam em uma única linha */
        }
        .table-report th, .table-report td {
            border: 1px solid #333333; /* Aplica borda em todas as células (cabeçalho e dados) */
            padding: 6px 8px;
            vertical-align: top;
            font-size: 10px; /* Reduz o tamanho da fonte para relatórios */
            line-height: 1.3;
        }

        .table-report th {
            text-align: center;
            font-weight: bold;
        }
        .table-report td {
            text-align: left;
        }
        .main-title {
            display: block; /* Trata como bloco para começar na borda */
            margin: 0;
            padding: 0;
            line-height: 1.3;
        }        
        .detail-block {
            margin-top: 4px; /* Pequeno espaçamento entre os blocos */
            line-height: 1.3;
        }

        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .badge-numero {
            background-color: #343a40; /* Dark */
            color: #ffffff;
            padding: 2px 5px;
            border-radius: 3px;
            font-weight: bold;
            display: inline-block;
            white-space: nowrap;
            margin-top: 2px;
        }
        .badge-anulado {
            background-color: #dc3545; /* Danger */
            color: #ffffff;
            padding: 2px 5px;
            border-radius: 3px;
            display: inline-block;
            margin-top: 2px;
        }
        .status-ativo { color: green; font-weight: bold; }
        .status-inativo { color: red; font-weight: bold; }
        .visibilidade-publico { color: #28a745; } /* Success */
        .visibilidade-restrito { color: #343a40; } /* Dark */
    </style>
</head>
<body>

       
<h1 style="color: #1a426f; text-align: center; padding-bottom: 10px; font-size: 18pt;">
    {{ $titulo1 }}
</h1>


<table class="table-report">

    <thead>
        {!!  $head1 !!}
    </thead>

    <tbody>
        {!!  $html1 !!}
    </tbody>
</table>
</body>
</html>