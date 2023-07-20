<?php

require_once 'database_connect.php';

$delete_object = $_POST['delete'];

$sql = $conn->prepare("SELECT id, parent_id, name FROM `objects`");
$sql->execute();
$arr = $sql->fetchAll(PDO::FETCH_ASSOC);
var_dump($arr);

if ($delete_object == "Космос")
{
    session_start();
    header("Location: index.php");
    $_SESSION['errors'] = 'cosmos';

    die;
}
foreach ($arr as $object)
{
    if ($delete_object == $object['name'])
    {
        $delete_object = $object;
        break;
    }
    
}
if (is_string($delete_object))
{
    session_start();
    header("Location: index.php");
    $_SESSION['errors'] = 'the_same_object_is_not_find';

    die;
}

echo 'это обьект который мы удаляем';
var_dump($delete_object);
/* 
$object - это потенциальный потомок для $obj, $obj передаем в функцию и по сути ищем, 
есть ли у него дети, затем выносим их айди в массив обьектов, которые потом удалим 
*/
$mass = array();
$f = function($obj, $arr) use (&$mass, &$f)
{
    foreach ($arr as $key => $object)
    {
        if ($obj['id'] == $object['parent_id'])
        {
            array_push($mass, $object['id']);
            $f($object, $arr);
        }
    }
};
$f($delete_object, $arr);
echo 'это массив всех потомков, которых мы тоже удалим';
array_push($mass, $delete_object['id']);
foreach ($mass as $object)
{
    $sql = $conn->prepare("DELETE FROM objects WHERE id = $object;");
    $sql->execute();
}

header("Location: index.php");