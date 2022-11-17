<?php

class Indexation
{
    static function indexDocuments($documents)
    {
        $invertedIndex = [];
        
        foreach ($documents as $document)
        {
            $filename = basename($document);
            $originalname = substr($filename, strpos($filename, "-") + 1);
            $file_content = file_get_contents($document);
            $file_content = preg_replace("/[^[:alnum:][:space:]á-úÁ-ÚñÑ-]/", "", $file_content);
            $snippet = substr($file_content, 0, 50);

            if ($file_content)
            {
                $token = strtok($file_content, " \t\r\n\v\f");
                while ($token) 
                {
                    $word = strtolower($token);

                    if (!array_key_exists($word, $invertedIndex))
                    {
                        $invertedIndex[$word]["frequency"] = 1;
                        $invertedIndex[$word]["documents"] = [];
                    }
                    else
                        $invertedIndex[$word]["frequency"]++;

                    if (!array_key_exists($filename, $invertedIndex[$word]["documents"]))
                    {
                        $invertedIndex[$word]["documents"][$filename]["name"] = $originalname;
                        $invertedIndex[$word]["documents"][$filename]["count"] = 1;
                        $invertedIndex[$word]["documents"][$filename]["snippet"] = $snippet;
                    }
                    else
                        $invertedIndex[$word]["documents"][$filename]["count"]++;

                    $token = strtok(" \t\r\n\v\f");
                }
            }
        }
        return $invertedIndex;
    }
}
