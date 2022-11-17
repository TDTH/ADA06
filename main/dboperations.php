<?php

class DBConnection
{
    const server    = "localhost";
    const username  = "root";
    const password = "admin";
    const dbname = "fulltextindex";
    const dbdocument = "document";

    private $connection = null;

    function __construct()
    {
        $this->initialize();
    }

    private function initialize()
    {
        $this->connection = new mysqli(self::server, self::username, self::password);
        if ($this->connection->connect_error)
        {
            die ("Conexión fallida: " . $this->connection->connect_error);
        }

        $query = "SHOW DATABASES LIKE '" . self::dbname . "'";
        $result = $this->connection->query($query);
        if ($result)
        {
            if (count($result->fetch_all()) === 0)
            {
                $query = "CREATE DATABASE " . self::dbname;
                if (!$this->connection->query($query))
                {
                    die ("Error al crear base de datos: " . $this->connection->error);
                }
                
                $this->connection->select_db(self::dbname);

                $query  = "SET NAMES utf8;";
                $query .= "SET CHARACTER SET utf8;";
                $query .= "CREATE TABLE " . self::dbdocument . " (documentID int not null auto_increment,
                                        docname varchar(100) not null unique,
                                        filename varchar(100) not null,
                                        content varchar(20000) not null,
                                        description varchar(50) not null,
                                        primary key (documentID));";

                $result = $this->connection->multi_query($query);
                if ($result) do {} while ($this->connection->next_result());
            }

            $this->connection->select_db(self::dbname);
        }
    }

    function saveDocumentData($documentData)
    {
        foreach ($documentData as $document => $data) {
            $sql = "INSERT INTO " . self::dbdocument . " (docname, filename, content, description)
                    VALUES ('$document', '".$data["name"]."', '".$data["content"]."', '".$data["snippet"]."')
                    ON DUPLICATE KEY UPDATE docname=docname;";
            $this->connection->query($sql);
        }
        $sql = "ALTER TABLE " . self::dbdocument . " ADD FULLTEXT (content);";
        $this->connection->query($sql);
    }

    function close()
    {
        $this->connection->close();
    }
}



