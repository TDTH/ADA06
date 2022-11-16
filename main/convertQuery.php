<?php
require_once '../index.php';
require_once 'conection.php';

$search = $_GET['search'];

$query = 'SELECT * FROM vocabulary WHERE ';
$defaultFields = ['products.product_name', 'products.quantity_per_unit', 'products.category'];

$search = strtolower($search);

$searchWords = explode(' ', $search);
if($searchWords[count($searchWords)-1] === ''){
    unset($searchWords[count($searchWords) - 1]);
}

$consecutiveWords = 0;
$pattern = '';

for ($i = 0; $i < count($searchWords); $i++) { 
    if($searchWords[$i] === 'and') {
        $query= $query . ' AND ';
        $consecutiveWords = 0;
        continue;
    }

    if ($searchWords[$i]==='or'){
        $query= $query . ' OR ';
        $consecutiveWords = 0;
        continue;
    }

    if ($searchWords[$i] === 'not'){
        if($searchWords[$i - 1] === 'and'){
            $query = $query . 'NOT ';
        }else{
            $query = $query . ' NOT ';
        }
        $consecutiveWords = 0;
        continue;
    }

    $withPattern = strpos($searchWords[$i], 'patron');
    if ($withPattern !== false){
        $i = addFunctionToQuery($i);
        addConditionsToQuery($pattern);
        $consecutiveWords = 0;
        continue;
    };

    if($consecutiveWords >= 1){
        $query = $query . ' OR ';
    }

    $currentKeyWord = "='".$searchWords[$i]."'";
    addConditionsToQuery($currentKeyWord);
    $consecutiveWords++;

}

/* echo '<div class="container">';
$result = mysqli_query($conection, $query);
if ($result && mysqli_num_rows($result) > 0) {
  echo '<h3>Resultados:</h3>';
  while ($row = mysqli_fetch_assoc($result)) {
    $tokenID = $row['tokenID'];
    $queryPosting = "SELECT * FROM posting WHERE tokenID = '$tokenID'";
    $resultPosting = mysqli_query($conection, $queryPosting);
    while ($rowPosting = mysqli_fetch_assoc($resultPosting)) {
        echo $rowPosting['documentID'];
    }
    echo '<div>';
    echo $row['token'];
    echo '</div>';
  }
} else {
    echo '<h3>Resultados:</h3>';
    echo '<h4>No se encontraron resultados de esa consulta</h4>';
}
echo '</div>'; */

$matrixTokens = [[]];
$indexMatrix = 0;

$queryDocs = "SELECT * FROM document";
$resultDocs = mysqli_query($conection, $queryDocs);
$NumDocs = mysqli_num_rows($resultDocs);

$result = mysqli_query($conection, $query);
if ($result && mysqli_num_rows($result) > 0) {
while ($row = mysqli_fetch_assoc($result)) {
    $idf = log10($NumDocs / $row['doccount']);

    /* $e = $row['doccount'];
    echo "numDocs: ".$NumDocs."<br>";
    echo "doccount: ".$e."<br>";
    echo "idf: ".$idf."<br>"; */
    
    $tokenID = $row['tokenID'];
    $token = $row['token'];
    $queryPosting = "SELECT * FROM posting WHERE tokenID = '$tokenID'";
    $resultPosting = mysqli_query($conection, $queryPosting);
    while ($rowPosting = mysqli_fetch_assoc($resultPosting)) {
        $docID = $rowPosting['documentID'];
        $countToken = $rowPosting['count'];
        $totalTokens = 0;
        $queryPostDoc = "SELECT * FROM posting WHERE documentID = '$docID'";
        $resultPostDoc = mysqli_query($conection, $queryPostDoc);
        while ($rowPostDoc = mysqli_fetch_assoc($resultPostDoc)) {
            $totalTokens += $rowPostDoc['count'];
        }
        //echo "total de tokens: ".$totalTokens."<br>";
        $tf = $countToken / $totalTokens;
        $tfidf = $tf * $idf;

        if(isset($mT[$docID])){
            $mT[$docID] = ($mT[$docID] + $tfidf) / 2;
        } else{
            $mT[$docID] = $tfidf;
        }

        //echo $rowPosting['documentID']."<br>";
        
    }

/*     echo '<div>';
    echo $row['token'];
    echo '</div>'; */
}
} else {
    echo '<h3>Resultados:</h3>';
    echo '<h4>No se encontraron resultados de esa consulta</h4>';
}

echo '<h3>Resultados:</h3>';

arsort($mT);
foreach($mT as $key => $val){
    $queryDoc = "SELECT * FROM document WHERE documentID = '$key'";
    $resultDoc = mysqli_query($conection, $queryDoc);
    $values = mysqli_fetch_array($resultDoc);
    echo '<div style="border: 1px solid black; padding: 1rem; margin-bottom:10px">';
    echo '<a href="download.php?file='.$values['docname'].'">'.$values['docname'].'</a><br><br>';
    echo '<em>rating: '.$val.'</em><br>';
    echo '</div>';
}

function addConditionsToQuery($keyWord){
    global $query, $defaultFields;
    $query = $query.'(';
    $query = $query ."token". $keyWord;
    $query = $query.')';
}

function addFunctionToQuery($wordPosition){
    global $searchWords;
    global $pattern;
    $pattern = " LIKE '%";

    $currentPosition = $wordPosition + 1;
    if(strpos($searchWords[$currentPosition] ,')') !== false){
        $pattern = $pattern .substr($searchWords[$currentPosition], 1, -1)."%'";
        return $currentPosition;
    }
    $pattern = $pattern .substr($searchWords[$currentPosition], 1);
    $currentPosition = $currentPosition + 1;
    while(strpos($searchWords[$currentPosition] ,')') === false){
        $pattern = $pattern .' '. $searchWords[$currentPosition];
        $currentPosition++;
    }
    $pattern = $pattern .' '. substr($searchWords[$currentPosition], 0, -1)."%'";
    return $currentPosition;
}