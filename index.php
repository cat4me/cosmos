<?php

    require_once 'database_connect.php';

    //$sql = "INSERT INTO objects (name, type) VALUES ('Земля', 'Планета')";
    //$conn->exec($sql);
?>


<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>космос</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/style.css?t=<?php echo(microtime(true).rand()); ?>" type="text/css" />
</head>
<body background="Purple Nebula 1 - 1024x1024.png"><center>
    
    <form class="" action="add_object.php" method="POST">
        <label for="universe"><h2>Вселенная</h2></label>
        <br>
        <input type="text" placeholder="Название вселенной" name="universe" title="только буквы, макс. длина = 100 символов" id="universe" class="rounded-input">
        <br>
        <label for="galaxy"><h2>Галактика</h2></label>
        <br>
        <input type="text" placeholder="Название галактики" name="galaxy" title="только буквы, макс. длина = 100 символов" id="galaxy" class="rounded-input">
        <br>
        <label for="starSystem"><h2>Звездная система</h2></label>
        <br>
        <input type="text" placeholder="Название звездной системы" name="starSystem" title="только буквы, макс. длина = 100 символов" id="starSystem" class="rounded-input">
        <br>
        <label for="star"><h2>Звезда</h2></label>
        <br>
        <input type="text" placeholder="Название звезды" name="star" title="только буквы, макс. длина = 100 символов" id="star" class="rounded-input">
        <br>
        <label for="planet"><h2>Планета</h2></label>
        <br>
        <input type="text" placeholder="Название планеты" name="planet" title="только буквы, макс. длина = 100 символов" id="planet" class="rounded-input">
        <br>
        <label for="satellite"><h2>Спутник</h2></label>
        <br>
        <input type="text" placeholder="Название спутника" name="satellite" title="только буквы, макс. длина = 100 символов" id="satellite" class="rounded-input">
        <br>
        <button type="submit">Добавить новый объект в космос</button>
        <br>
        <br>
    </form>

    <form class="" onSubmit="return confirm('Если у объекта есть потомки, они тоже будут удалены. Вы хотите продолжить?');" action="delete_object.php" method="POST">
        <input type="text" placeholder="Удалить объект" name="delete" required title="только буквы, макс. длина = 100 символов" id="delete" class="rounded-input">
        <br>
        <button type="submit" class="delete-button">Удалить объект из космоса</button>

    </form>

    
</body></center>




<?php

    session_start();
    switch($_SESSION['errors'])
    {
        case 'empty': ?>
            <script>alert("Заполните хотя бы одно поле для добавления объектов")</script>
            <?php $_SESSION['errors'] = ''; ?>
            <?php break; ?>
        <?php case 'the_same_object_is_not_find': ?>
            <script>alert("Такого объекта не существует")</script>
            <?php $_SESSION['errors'] = ''; ?>
            <?php break; ?>
        <?php case 'cosmos': ?>
            <script>alert("К сожалению, Вы не можете удалить космос, но это даже к лучшему...")</script>
            <?php $_SESSION['errors'] = ''; ?>
            <?php break; ?>
        <?php case 'find_the_same_object': ?>
            <script>alert("Такой объект уже существует")</script>
            <?php $_SESSION['errors'] = ''; ?>
            <?php break; ?>
        <?php default: ?>
        <?php break;
    }

    
    //забираем таблицу с обьектами из бд и преобразуем в ассоциативный список
    $sth = $conn->prepare("SELECT * FROM `objects` ORDER BY `id`");
    $sth->execute();
    $arr = $sth->fetchAll(PDO::FETCH_ASSOC);
    //print_r($arr);
    if (empty($arr))
    {
        $sql = "INSERT INTO objects (name, type) VALUES ('Космос', 'Пустота')";
        $conn->exec($sql);
    }
    print "<br><br>";

    $sth = $conn->prepare("SELECT * FROM `objects` ORDER BY `id`");
    $sth->execute();
    $arr = $sth->fetchAll(PDO::FETCH_ASSOC);
    //обрабатываем массив, получаем новый массив, но уже со структурой дерева
    $new = array();

    foreach ($arr as $a)
    {
        $new[$a['parent_id']][] = $a;
    }
    $tree = createTree($new, array($arr[0]));
    //print_r($tree);
      
    function createTree(&$list, $parent)
    {
        $tree = array();
        
        foreach ($parent as $k=>$l)
        {
            if(isset($list[$l['id']]))
            {
                $l['children'] = createTree($list, $list[$l['id']]);
            }
             $tree[] = $l;
        } 
        return $tree;
    }

    //print_r($new);
    $tempname = "";
    function tree($ar, $parentobj = "неизвестно")
    {
        foreach ($ar as $key => $obj)
        {
            if ($key == 'children')
            {
                echo '<center>';
                echo '<h1>' . '|' . '<br>' . '|' . '<br>'  . '\\' . '/' . '</h1>';
                echo '</center>';
                tree($obj, $ar['name']);
            }
            else if (is_array($obj))
            {
                if ($parentobj != "неизвестно")
                {
                    tree($obj, $parentobj);
                }
                else
                {
                    tree($obj);
                }
            }
            else
            {
                if ($key == "name")
                {
                    if ($key == "name" and $obj == "Космос")
                    {
                        $parentobj = "Бесконечная бездна";
                    }
                    $tempname = $obj;
                    
                }
                if ($key == "type")
                {
                    echo '<center>' . '<pre>' . '<h1>'. $tempname . '</h1>' . '<h4>' . $obj . ' (находится в: ' . $parentobj . ')' . '</h4>' . '</pre>' . '</center>';
                }
            }
        }
    }

    tree($tree);

/*            ЗАПРОС ЧТОБЫ ПОКАЗАТЬ ВСЕ ПУТИ ДО ВСЕХ НЕБЕСНЫХ ТЕЛ ОТ КОРНЕВОГО УЗЛА
    WITH RECURSIVE descendants AS
(
SELECT id, CAST(id AS CHAR(500)) AS path
FROM objects
WHERE id = 6
UNION ALL
SELECT t.id, CONCAT(d.path, ',', t.id)
FROM descendants d, objects t
WHERE t.parent_id=d.id
)
SELECT * FROM descendants ORDER BY path;
*/

