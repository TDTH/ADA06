<?php include_once(__DIR__."/main/main.php") ?>
<!DOCTYPE html>
<html>
    <body>
        <form enctype="multipart/form-data" method="post">
            Seleccione archivos para agregar al índice:
            <input type="file" name="files[]" accept="text/plain" multiple>
            <input type="submit" value="Upload file">
        </form>
    </body>
</html>
