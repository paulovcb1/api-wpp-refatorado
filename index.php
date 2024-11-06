<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload de Planilha</title>
    <link rel="stylesheet" href="style/style2.css">

</head>
<body>
    <div class="all">
        <h2>Upload de Planilha</h2>
            <div class="recive">
                <form action="processar_planilhas.php" method="post" enctype="multipart/form-data">
                    <label for="file">Escolha a planilha do EXCEL (.xlsx):</label>
                    <input class="inserir" accept=".xlsx" type="file" name="file" id="file" required>
                    <br><br>
                    <input class="button" type="submit" value="Carregar Planilha">
                </form>
            </div>
    </div>
</body>
</html>
