<?php
session_start();
$data = $_SESSION['dados'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecionar Coluna</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    

    <div class="form"> 
        <h1>Selecionar coluna com telefones</h1>
        <form id="formulario" action="enviar_para_api.php" method="post">
            <label for="coluna_telefone">Escolha a coluna:</label>
            <select name="coluna_telefone" id="coluna_telefone">
                <?php
                // Gerar opções para o select com base nos nomes das colunas
                $nomes_colunas = $data[1]; // Supondo que a primeira linha contém os cabeçalhos
                foreach ($nomes_colunas as $index => $nome_coluna) {
                    echo "<option value='$index'>$nome_coluna</option>";
                }
                ?>
            </select>
            <br><br>
            <label class="mensagem" for="mensagem">Mensagem:</label><br>
            <textarea name="mensagem" id="mensagem" rows="4" cols="50" required></textarea>
            <br><br>
            <input class="botao" type="submit" value="Enviar">
        </form>
        <div id="loading">Enviando...</div> <!-- Mensagem de carregamento -->
    </div>

    <script>
        document.getElementById('formulario').addEventListener('submit', function() {
            document.getElementById('loading').style.display = 'block';
        });
    </script>
</body>
</html>
