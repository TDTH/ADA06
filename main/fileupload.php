<?php

class FileUpload
{

    function __construct()
    {
        $this->uploaddir = $_SERVER["DOCUMENT_ROOT"] . "/file_uploads/";

        if (!file_exists($this->uploaddir))
            if (!mkdir($this->uploaddir, 0755))
                die ("No se pudo crear directorio de archivos.");
    }

    function uploadFiles($files)
    {
        $documensToIndex = [];

        foreach ($files["error"] as $index => $error) 
        {
            if ($error === UPLOAD_ERR_OK)
            {
                $filename = $files["name"][$index];
                $tmpname  = $files["tmp_name"][$index];

                $sha1hash = sha1_file($tmpname);
                $uploadpath = $this->uploaddir . $sha1hash . "-" . $filename;

                if (!file_exists($uploadpath))
                {
                    move_uploaded_file($tmpname, $uploadpath);
                    $documensToIndex[] = $uploadpath;
                }
            }
        }

        return $documensToIndex;
    }
}

