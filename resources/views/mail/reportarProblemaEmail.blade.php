<x-mail::message>
    
    <h1>{{$emaildata->dados}}</h1>

    Problema Reportado, dados informados
    De .: {{$emaildata->contact_name}}
    Email.: {{$emaildata->contact_email}}
    Assunto.: {{$emaildata->contact_assunto}}
    Mensagem.: {{$emaildata->contact_message}}
</x-mail::message>
