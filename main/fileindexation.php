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

            if ($file_content)
            {
                $token = strtok($file_content, " \t\r\n\v\f");
                while ($token) 
                {
                    $word = strtolower($token);
                    //$invertedIndex[$word]["documents"][] = $originalname;
                    //$filename = basename($document);
                    //$originalname = substr($filename, strpos($filename, "-") + 1);

                    if (!array_key_exists($word, $invertedIndex))
                    {
                        $invertedIndex[$word]["frequency"] = 1;
                        $invertedIndex[$word]["documents"] = [];
                    }
                    else
                        $invertedIndex[$word]["frequency"]++;

                    if (!array_key_exists($filename, $invertedIndex[$word]["documents"]))
                        $invertedIndex[$word]["documents"][$filename] = 1;
                    else
                        $invertedIndex[$word]["documents"][$filename]++;

                    $token = strtok(" \t\r\n\v\f");
                }
            }
        }

        
        foreach ($invertedIndex as $token => $value)
        {
            print("<p>\"".$token."\" tiene una frecuencia total = ".$value["frequency"]." y aparece en:</p><ul>");
            foreach ($value["documents"] as $index => $c)
            {
                print("<li>$index $c ".($c == 1 ? "vez" : "veces")."</li>");
            }
            print("</ul>");
        }
        print("pipapopo"); 
        return $invertedIndex;
    }
}
