<?php
// atualizar-versao.php
date_default_timezone_set('America/Sao_Paulo');
// 1. Pega a contagem de commits do Git
$commitCount = (int) shell_exec('git rev-list --count HEAD');
$nextCommitCount = $commitCount + 1;

// Define a versão (ex: 1.0.124)
$newVersion = "1.0." . $nextCommitCount;

// 2. Pega o hash atual (se houver anterior)
$shortHash = trim(shell_exec('git rev-parse --short HEAD') ?? '0000000');
$timestamp = date('Y-m-d H:i:s');

// 3. Monta o conteúdo do arquivo
$content = "<?php\n\nreturn [\n";
$content .= "    'number' => '{$newVersion}',\n";
$content .= "    'hash' => '{$shortHash}',\n";
$content .= "    'timestamp' => '{$timestamp}',\n";
$content .= "];\n";

// 4. Grava no arquivo de configuração do Laravel
file_put_contents(__DIR__ . '/config/version.php', $content);

echo "\n=======================================================\n";
echo "✅ Versão gerada com sucesso para o próximo Commit: $newVersion\n";
echo "=======================================================\n\n";