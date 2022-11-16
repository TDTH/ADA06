<?php

include_once(__DIR__."/dboperations.php");
include_once(__DIR__."/fileupload.php");
include_once(__DIR__."/fileindexation.php");

if (isset($_FILES["files"]))
{
    $connection = new DBConnection();
    $fileupload = new FileUpload();

    $documentsToIndex = $fileupload->uploadFiles($_FILES["files"]);
    $invertedIndex = Indexation::indexDocuments($documentsToIndex);
    $connection->saveInvertedIndex($invertedIndex);

    $connection->close();
}


