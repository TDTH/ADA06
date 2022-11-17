<?php

include_once(__DIR__."/dboperations.php");
include_once(__DIR__."/fileupload.php");
include_once(__DIR__."/fileindexation.php");

if (isset($_FILES["files"]))
{
    $connection = new DBConnection();
    $fileupload = new FileUpload();

    $documentsToIndex = $fileupload->uploadFiles($_FILES["files"]);
    $documentData = Indexation::indexDocuments($documentsToIndex);
    $connection->saveDocumentData($documentData);

    $connection->close();
}


