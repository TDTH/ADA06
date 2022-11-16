<?php

class DBConnection
{
    const server    = "localhost";
    const username  = "root";
    const password = "";
    const dbname = "invertedindex";
    const dbvocabulary = "vocabulary";
    const dbposting = "posting";
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

                $query  = "CREATE TABLE " . self::dbvocabulary. "(tokenID int not null auto_increment,
                                        token varchar(100) not null unique, 
                                        doccount int not null,
                                        totalfreq int not null,
                                        primary key (TokenID));";

                $query .= "CREATE TABLE " . self::dbdocument . " (documentID int not null auto_increment,
                                        docname varchar(100) not null unique,
                                        description varchar(51) not null,
                                        primary key (documentID));";

                $query .= "CREATE TABLE " . self::dbposting ." (postingID int not null auto_increment,
                                        tokenID int not null,
                                        documentID int not null,
                                        count int not null,
                                        PRIMARY KEY (postingID),
                                        FOREIGN KEY (tokenID) references " . self::dbvocabulary . " (tokenID),
                                        foreign key (documentID) references " . self::dbdocument . " (documentID));";

                $result = $this->connection->multi_query($query);
                if ($result) do {} while ($this->connection->next_result());
            }

            $this->connection->select_db(self::dbname);
        }
    }

    function saveInvertedIndex($invertedIndex)
    {
        foreach ($invertedIndex as $token => $value) {
            $doccount = count($value["documents"]);
            $totalfreq = $value["frequency"];

            $sql = "INSERT INTO " . self::dbvocabulary . " (token, doccount, totalfreq) 
                    VALUES ('$token', '$doccount', '$totalfreq')
                    ON DUPLICATE KEY UPDATE doccount=doccount+$doccount, totalfreq=totalfreq+$totalfreq;";
            $this->connection->query($sql);

            foreach ($value["documents"] as $document => $count) 
            {
                $sql = "INSERT INTO " . self::dbdocument . " (docname, description)
                    VALUES ('$document', 'aqui va la descripcion de cada documento')
                    ON DUPLICATE KEY UPDATE docname=docname;";
                $this->connection->query($sql);

                $sql = "INSERT INTO " . self::dbposting . " (tokenID, documentID, count)
                    VALUES (
                        (SELECT tokenID from " . self::dbvocabulary . " WHERE token='$token'),
                        (SELECT documentID from " . self::dbdocument . " WHERE docname='$document'),
                        '$count'
                    );";
                $this->connection->query($sql);
            }
        }
    }

    function close()
    {
        $this->connection->close();
    }
}



