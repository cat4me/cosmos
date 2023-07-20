<?php

require_once 'database_connect.php';

if (empty($_POST['universe']) and empty($_POST['galaxy']) and empty($_POST['starSystem']) and empty($_POST['star']) and empty($_POST['planet']) and empty($_POST['satellite']))
{
    session_start();
    header("Location: index.php");
    $_SESSION['errors'] = 'empty';

    die;
}
$universe = $_POST['universe'];
$galaxy = $_POST['galaxy'];
$starSystem = $_POST['starSystem'];
$star = $_POST['star'];
$planet = $_POST['planet'];
$satellite = $_POST['satellite'];


//масив переданных оьектов для удобной обработки
$mass = array($universe, $galaxy, $starSystem, $star, $planet, $satellite);

$lastobj = 0;
//ищем конечный обьект потенциальной цепочки обьектов
foreach ($mass as $key => $obj)
{
    if ($obj != null)
    {
        $lastobj = $obj;
    }
}

$sql = "SELECT * FROM objects";
$result = $conn->query($sql);
//проверяем, есть ли уже такой обьект в БД

foreach ($result as $row)
{
    if ($lastobj == $row['name'])
    {
        session_start();
        header("Location: index.php");
        $_SESSION['errors'] = 'find_the_same_object';

        die;
    }
}

/*функция, которую будем использовать ко всем обьектам, кроме последнего. если такой обьект уже есть, 
  мы возвращаем его id, чтобы задать его как parent_id для потомка*/

function find($obj, $result, $conn)
{
    $sql = "SELECT * FROM objects";
    if ($result = $conn->query($sql)) 
    {
        foreach ($result as $row)
        {
            if ($obj == $row["name"])
            {
                return $row["id"];
            }
            
        }
    }
    return false;
}
$tempid = null;
$parent_id = 1;
//снова проходимся по всем обьектам и вытаскиваем айдишники, в случае, если такой обьект есть, то его мы задаем для следующего как родителя
$count = 0;
$type = "";
foreach ($mass as $key => $obj)
{
    if ($obj != null)
    {
        if(find($obj, $result, $conn))
        {
            $parent_id = find($obj, $result, $conn);
            echo $parent_id;
        }
        else
        {
            switch($count)
            {
                case 0:
                    $type = "Вселенная";
                    break;
                case 1:
                    $type = "Галактика";
                    break;
                case 2:
                    $type = "Звездная система";
                    break;
                case 3:
                    $type = "Звезда";
                    break;
                case 4:
                    $type = "Планета";
                    break;
                case 5:
                    $type = "Спутник";
                    break;
                default:
                    break;
                }
            var_dump($mass[$key]);
            $sql = "INSERT INTO objects (parent_id, name, type) VALUES ($parent_id, '$obj', '$type')";
            $conn->exec($sql);
            $sql = "SELECT * FROM objects";
            $result = $conn->query($sql);
            $parent_id = find($obj, $result, $conn);
        }
    }
    $count++;
}
header("Location: index.php");
//$sql = "INSERT INTO objects (name, type) VALUES ('Земля', 'Планета')";
//$conn->exec($sql);