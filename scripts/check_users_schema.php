<?php
$host='127.0.0.1';$port=3306;$db='information_schema';$user='root';$pass='root';
try{
    $pdo=new PDO("mysql:host=$host;port=$port;dbname=$db",$user,$pass);
    $st=$pdo->prepare('SELECT COLUMN_NAME, COLUMN_TYPE, DATA_TYPE, COLUMN_DEFAULT, IS_NULLABLE FROM COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=?');
    $st->execute(['ecommerce_db','users']);
    $rows=$st->fetchAll(PDO::FETCH_ASSOC);
    if(!$rows){
        echo "No rows returned; table may not exist.\n";
    }
    foreach($rows as $r){
        echo $r['COLUMN_NAME'] . "\t" . $r['COLUMN_TYPE'] . "\t" . $r['DATA_TYPE'] . "\t" . ($r['COLUMN_DEFAULT'] ?? 'NULL') . "\t" . $r['IS_NULLABLE'] . "\n";
    }
}catch(Exception $e){
    echo 'Error: ' . $e->getMessage() . "\n";
}
