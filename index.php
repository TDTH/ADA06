<?php include_once(__DIR__."/main/main.php") ?>
<!DOCTYPE html>
<html>
    <body>
        <form enctype="multipart/form-data" method="post">
            Seleccione archivos para agregar al índice:
            <input type="file" name="files[]" accept="text/plain" multiple>
            <input type="submit" value="Upload file">
        </form>
        <h2>Buscar entre archivos subidos</h2>
        <form class="input-search" action="../main/convertQuery.php">
            <input type="search" id="input-article" name="search" placeholder="Buscar">
            <button type="submit" class="btn-search">Buscar</button>
        </form>
        <div class="documents"></div>
    </body>
</html>
