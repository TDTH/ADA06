<?php
require_once '../index.php';
require_once 'conection.php';

$search = $_GET['search'];

$query = 'SELECT *, MATCH(content) AGAINST(';

$query = $query . '\'' . $search . '\' IN BOOLEAN MODE) AS Score FROM document WHERE MATCH(content) AGAINST (' . '\'' . $search . '\' IN BOOLEAN MODE) ORDER BY score DESC;';

//echo $query;

$result = mysqli_query($conection, $query);
while ($row = mysqli_fetch_assoc($result)) {
    echo '<div style="border: 1px solid black; padding: 1rem; margin-bottom:10px">';
    echo '<a href="download.php?file='.$row['docname'].'">'.$row['filename'].'</a><br><br>';
    echo '<p>'.$row['description'].'</p><br>';
    echo '<em>rating: '.$row['Score'].'</em><br>';
    echo '</div>';
}