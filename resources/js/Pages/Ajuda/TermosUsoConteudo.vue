<script setup>
// Definimos as propriedades que o componente precisa receber de fora
defineProps({
    appUrl: {
        type: String,
        required: true
    },
    logoEmpresa: {
        type: String,
        required: true
    }
});
</script>

<template>
    <div class="flex flex-col items-center text-center border-b border-comum/20 pb-6 mb-6">
        <img alt="Logo" class="max-w-xs md:max-w-sm mb-4 rounded-lg" :src="`${appUrl}/build/assets/images/company/${logoEmpresa}_005.png`"> 
        <h2 class="text-2xl font-bold text-texto-claro">Termos e Condições de Uso</h2>
    </div> 

    <div class="space-y-6 text-sm text-justify leading-relaxed">
        <div>
            <h3 class="text-lg font-bold text-primary mb-2">Termos de Uso</h3>
            <p>Os presentes Termos de Uso têm como objetivo regular a utilização dos serviços oferecidos através de nossos sistemas e plataformas mobile. Ao utilizar nossos serviços, você, usuário, declara que leu, entendeu e aceitou integralmente estes Termos e Condições de Uso.</p> 
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">1. Aceitação dos Termos</h4>
            <p>A utilização de qualquer serviço online disponibilizado por nosso site implica na sua aceitação expressa e automática destes Termos e Condições de Uso, que se encontram vigentes na data de acesso. Alertamos que estes Termos podem ser modificados a qualquer momento, em virtude de alterações na legislação, atualização de nossos serviços ou adoção de novas tecnologias.</p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">2. Cadastro e Acesso</h4>
            <p class="mb-2">A utilização de alguns de nossos serviços e canais pode ser restrita e condicionada ao prévio registro do usuário. Para acessar áreas restritas, será necessário criar uma conta com senha. Você é responsável pela veracidade e precisão das informações fornecidas no cadastro e assume total responsabilidade pela guarda e sigilo de seu login e chave de acesso.</p>
            <p>O compartilhamento de credenciais de acesso com terceiros é expressamente proibido. O cadastro de informações falsas pode resultar na suspensão ou interrupção do seu acesso, sem aviso prévio.</p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">3. Conduta do Usuário e Responsabilidades</h4>
            <p class="mb-2">Ao utilizar nosso portal, você se compromete a fazer uso adequado dos conteúdos e serviços. Você concorda que é proibido:</p>
            <ul class="list-disc pl-6 flex flex-col gap-1 mb-2">
                <li>Utilizar o conteúdo do portal para fins comerciais sem autorização expressa.</li>
                <li>Publicar ou disseminar material ilegal, ameaçador, calunioso, difamatório ou que contenha incitação à violência ou qualquer tipo de discriminação (sexual, racial, étnica, religiosa, política, etária, social, etc.).</li>
                <li>Tentar ou realizar acesso não autorizado a dispositivos, sistemas ou redes.</li>
                <li>Transmitir dados que possam causar falhas em serviços ou equipamentos do portal.</li>
                <li>Distribuir mensagens não solicitadas (spam) ou de natureza em massa, via e-mail ou outros canais interativos.</li> 
            </ul>
            <p>Você é responsável pela precisão e veracidade dos dados informados e reconhece que a inconsistência pode levar à impossibilidade de acessar o site ou os serviços, além de sanções administrativas, civis e penais previstas na legislação brasileira.</p>
        </div>

        <div class="border-t border-comum/20 pt-4">
            <h3 class="text-lg font-bold text-primary mb-2">Política de Privacidade e Cookies</h3>
            <p>A sua privacidade e o sigilo de suas informações são extremamente importantes para nós. Esta política descreve como tratamos os seus dados pessoais e o uso de cookies em nosso portal.</p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">1. Coleta e Tratamento de Informações Pessoais</h4>
            <p>Solicitamos informações pessoais, como seu nome completo, Data de Nascimento, CPF, RG, e-mail, telefone e Endereço, apenas quando são essenciais para fornecer um serviço específico. A coleta é feita de forma justa e legal, com o seu pleno conhecimento e consentimento.</p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">2. Finalidade da Coleta e Uso dos Dados</h4>
            <p class="mb-2">Os dados que coletamos têm a finalidade de:</p>
            <ul class="list-disc pl-6 flex flex-col gap-1">
                <li><strong>Identificação e Autenticação:</strong> Para criar e gerenciar seu perfil de usuário.</li>
                <li><strong>Comunicação:</strong> Para enviar notificações, informações sobre o serviço e comunicações.</li>
                <li><strong>Cumprimento de Obrigações Legais:</strong> Para emissão de notas fiscais ou outros documentos exigidos por lei.</li>
                <li><strong>Melhoria de Serviços:</strong> Para entender o perfil de nossos usuários e aprimorar a experiência de uso.</li>
            </ul>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">3. Compartilhamento de Dados</h4>
            <p class="mb-2">Não vendemos, alugamos ou trocamos suas informações pessoais. Podemos compartilhar seus dados apenas com terceiros que são essenciais para a prestação dos nossos serviços, como:</p>
            <ul class="list-disc pl-6 flex flex-col gap-1">
                <li><strong>Provedores de serviços:</strong> Empresas de hospedagem, platforms de pagamento, serviços de envio de e-mail.</li>
                <li><strong>Autoridades legais:</strong> Se exigido por lei ou ordem judicial.</li>
            </ul>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">4. Armazenamento e Proteção de Dados</h4>
            <p>Mantemos suas informações pessoais armazenadas apenas pelo tempo necessário para cumprir as finalidades descritas nesta política, ou conforme exigido por lei. Implementamos medidas de segurança técnicas e organizacionais para proteger seus dados contra perda, roubo, acesso não autorizado, divulgação ou modificação.</p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">5. Direitos do Titular dos Dados</h4>
            <p class="mb-2">Em conformidade com a LGPD, você tem o direito de:</p>
            <ul class="list-disc pl-6 flex flex-col gap-1 mb-2">
                <li><strong>Acessar:</strong> Solicitar uma cópia dos seus dados pessoais que temos em nosso poder.</li>
                <li><strong>Corrigir:</strong> Pedir a alteração de dados incompletos, incorretos ou desatualizados.</li>    
                <li><strong>Excluir:</strong> Solicitar a eliminação de seus dados, salvo em casos de obrigação legal.</li>
                <li><strong>Revogar o Consentimento:</strong> Retirar seu consentimento para o uso de seus dados a qualquer momento.</li>
            </ul>
            <p>Para exercer esses direitos, entre em contato conosco através do Canais de Contato e Ouvidoria.</p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">6. Links para Sites Externos</h4>
            <p>Nosso portal pode conter links para sites de terceiros. Esta Política de Privacidade se aplica apenas ao nosso Portal. Não nos responsabilizamos pelo conteúdo ou pelas políticas de privacidade de sites externos.</p>
        </div>

        <div class="border-t border-comum/20 pt-4">
            <h3 class="text-lg font-bold text-primary mb-2">Política de Cookies</h3>
            <p>Esta política explica como e por que usamos cookies e outras tecnologias de rastreamento para melhorar sua experiência em nosso site.</p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">1. O que são Cookies?</h4>
            <p>Cookies são pequenos arquivos de texto armazenados no seu computador ou dispositivo móvel quando você visita um site. Eles são amplamente utilizados para otimizar a funcionalidade de sites e para coletar informações de navegação, ajudando a melhorar a sua experiência.</p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">2. Como Usamos os Cookies</h4>
            <p>Utilizamos cookies por diversas razões para garantir que nosso site funcione de maneira ideal e ofereça a melhor experiência possível. Eles são essenciais para manter você logado, lembrar suas preferências e personalizar o conteúdo que você vê. Infelizmente, a desativação dos cookies pode afetar negativamente a funcionalidade de certas áreas do site.</p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">3. Gerenciamento de Cookies</h4>
            <p class="mb-2">Você tem o controle sobre os cookies. A maioria dos navegadores permite que você os gerencie através das configurações. Você pode aceitar, recusar ou excluir cookies a qualquer momento, mas esteja ciente de que isso pode desativar recursos importantes do nosso site.</p>
            <p class="text-xs">
                Veja as definições sobre cookies dos principais navegadores: 
                <a href="https://privacy.microsoft.com/pt-BR/windows-10-microsoft-edge-and-privacy" target="_blank" class="text-primary hover:underline ml-1">Edge</a> / 
                <a href="https://support.google.com/accounts/answer/61416?hl=pt" target="_blank" class="text-primary hover:underline mx-1">Google Chrome</a> / 
                <a href="https://support.mozilla.org/pt-BR/kb/desative-cookies-no-firefox-parar-rastreamento" target="_blank" class="text-primary hover:underline mx-1">Firefox</a> / 
                <a href="https://help.opera.com/en/latest/security-and-privacy/" target="_blank" class="text-primary hover:underline mx-1">Opera</a> / 
                <a href="https://support.apple.com/pt-br/guide/mdm/mdmf7d5714d4/1/web/1.0" target="_blank" class="text-primary hover:underline ml-1">Safari</a>
            </p>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">4. Cookies que Definimos</h4>
            <ul class="list-disc pl-6 flex flex-col gap-1">
                <li><strong>Cookies de Autenticação:</strong> Usamos cookies para gerenciar o processo de login e manter você logado, evitando a necessidade de fazer login a cada nova página.</li>
                <li><strong>Cookies de Preferências do Usuário:</strong> Armazenamos suas preferências de navegação para que o site funcione de acordo com suas escolhas, garantindo uma experiência personalizada.</li>
                <li><strong>Cookies de Formulários e Cadastros:</strong> Para lembrar detalhes do usuário e facilitar o preenchimento de formulários, como os de contato.</li>
            </ul>
        </div>

        <div>
            <h4 class="font-bold text-base text-texto-claro mb-1">5. Cookies de Terceiros</h4>
            <p class="mb-2">Em alguns casos, usamos serviços de terceiros para melhorar nosso site, e eles também podem utilizar cookies.</p>
            <ul class="list-disc pl-6">
                <li><strong>Google Analytics:</strong> Usamos o Google Analytics para entender como os visitantes interagem com o nosso site, o que nos ajuda a melhorar o design e o conteúdo. As informações coletadas são anônimas.</li>
            </ul>
        </div>

        <div class="border-t border-comum/20 pt-4">
            <h4 class="font-bold text-base text-texto-claro mb-1">Disposições Gerais</h4>
            <p>Podemos, a qualquer momento e sem aviso prévio, alterar ou extinguir qualquer conteúdo ou serviço do portal, bem como modificar sua estrutura visual. O uso continuado de nosso site será considerado como sua aceitação das nossas práticas em torno de privacidade e informações pessoais.</p>
            <p class="text-xs font-semibold mt-2 text-texto-claro/50">Esta política é efetiva a partir de Junho/2026.</p>
        </div>
    </div>
</template>