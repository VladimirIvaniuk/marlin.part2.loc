<?php
class DB{
    public $pdo;
    public $table;
    function __construct()
    {
        $this->pdo= new PDO('mysql:host=localhost;dbname=marlin_part_2;', "root", "root");
    }
    function getAll(){
        $sql = "SELECT * FROM comments ORDER BY id DESC ";
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    function add($data){
        $sql = "INSERT INTO comments (name, comment, date) VALUES (:name, :comment, :date)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute($data);
        $query = $this->pdo->query("SELECT LAST_INSERT_ID()");
        $id = $query->fetchColumn();
        return $id;
    }
}
function dump($arr, $var_dump = false)
{
    echo "<pre style='background: #222;color: silver; font-weight: 800; padding: 20px; border: 10px double blue;'>";
    if ($var_dump) {
        var_dump($arr);
    } else {
        print_r($arr);
    }
    echo "</pre>";
}
function redirect_to($path)
{
    header('Location: ' . $path);
    exit;
}