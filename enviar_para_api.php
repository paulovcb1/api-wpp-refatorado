<?php
session_start();

function configurarMensagem($mensagem) {
    // Garantir que a mensagem está em UTF-8 e remover caracteres de controle
    $mensagem = mb_convert_encoding($mensagem, 'UTF-8', 'auto');
    return preg_replace('/[\x00-\x1F\x7F]/u', '', $mensagem);
}

function formatarTelefone($numero, $codigo_pais = '55') {
    // Remover prefixo e manter apenas números
    $telefone_formatado = preg_replace('/^\(\d+°\)\s*/', '', $numero);
    $telefone_formatado = preg_replace('/\D/', '', $telefone_formatado);

    // Adicionar código do país se o número tiver ao menos DDD + número local
    if (strlen($telefone_formatado) >= 10) {
        $telefone_formatado = $codigo_pais . $telefone_formatado;
    }
    return $telefone_formatado;
}

function enviarMensagem($telefone_formatado, $mensagem, $url, $instance, $token) {
    // Configurar dados para enviar para a API
    $data_api = [
        'instance' => $instance,
        'to' => $telefone_formatado,
        'token' => $token,
        'message' => $mensagem
    ];

    $options = [
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'content' => http_build_query($data_api, '', '&', PHP_QUERY_RFC3986)
        ]
    ];

    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

function verificarResposta($resultado_api) {
    // Decodificar JSON da resposta e verificar erros
    $resposta = json_decode($resultado_api);
    return json_last_error() === JSON_ERROR_NONE && isset($resposta->erro) ? 'Erro ao Enviar' : 'Enviado com Sucesso';
}

function exibirTabelaResultados($resultados) {
    echo '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Status de Envio</title>
        <style>
            body { font-family: Arial, sans-serif; }
            #status { margin: 20px; font-size: 1.2em; color: #007BFF; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
            th { background-color: #f4f4f4; }
            .button { 
                display: inline-block; 
                padding: 10px 20px; 
                margin: 20px 0; 
                font-size: 1em; 
                color: #fff; 
                background-color: #007BFF; 
                border: none; 
                border-radius: 5px; 
                text-decoration: none; 
                text-align: center; 
            }
            .button:hover { background-color: #0056b3; }
        </style>
    </head>
    <body>
        <a href="index.php" class="button">Voltar para a Página Inicial</a>
        <table>
            <tr><th>ID</th><th>Telefone</th><th>Status</th></tr>';

    foreach ($resultados as $resultado) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($resultado['id']) . '</td>';
        echo '<td>' . htmlspecialchars($resultado['telefone']) . '</td>';
        echo '<td>' . htmlspecialchars($resultado['status']) . '</td>';
        echo '</tr>';
    }

    echo '</table></body></html>';
}

if (isset($_SESSION['dados'], $_POST['coluna_telefone'], $_POST['mensagem'])) {
    $data = $_SESSION['dados'];
    $coluna_telefone = $_POST['coluna_telefone'];
    $mensagem = configurarMensagem($_POST['mensagem']);

    $url = "http://api.wordmensagens.com.br/send-text";
    $instance = "1L9240924051415OWN802";
    $token = "0U18P-Z2N-0493S";
    $resultados = [];
    $id = 1;

    foreach ($data as $index => $row) {
        if ($index === 0 || !isset($row[$coluna_telefone])) continue;

        $numeros = preg_split('/\s*\/\s*/', $row[$coluna_telefone]);
        foreach ($numeros as $numero) {
            $telefone_formatado = formatarTelefone($numero);

            if (!empty($telefone_formatado) && strlen($telefone_formatado) >= 12) {
                $resultado_api = enviarMensagem($telefone_formatado, $mensagem, $url, $instance, $token);
                $status_envio = verificarResposta($resultado_api);
            } else {
                $status_envio = 'Número inválido ou vazio';
            }

            $resultados[] = [
                'id' => $id++,
                'telefone' => $telefone_formatado ?: $numero,
                'status' => $status_envio
            ];
        }
    }

    exibirTabelaResultados($resultados);
} else {
    echo "Erro: dados necessários não encontrados na sessão.";
}
?>
