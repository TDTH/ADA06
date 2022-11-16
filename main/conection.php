<?php
    $result = "";

    $conection = mysqli_connect("localhost","root","","invertedindex");

    if(mysqli_connect_errno()){
        $result = "No se puede realizar la conexión PHP-MYSQL";
        echo $result;
    }
?>