<?php
    // запрос на конкретное упражнение
    // https://hmns.in/mishkajane/newVrs/get_one.php?uid=333&modex=33&stream=5
    // Первая цифра - номер тренинга, вторая - номер упражнения
    // Если пользователя нет, то шли 0;
    // response: 
    // [1:5]
    // example: 2
    
    /* ОБЪЯВЛЯЮ ПЕРЕМЕННЫЕ */
    $uid;
    $modex;
    $stream;
    $tableName;
    $modexCount = [];
    
    /* ПОЛУЧАЮ ДАННЫЕ ИЗ GET ЗАПРОСА */
    getUserData($uid, $modex, $stream);
    $tableName = 'users_'.$stream;
    
    /* ОТКРЫВАЮ СОЕДИНЕНИЕ С БАЗОЙ */
    $mysql = openConnection();
    
    /* ПОЛУЧАЮ КОЛИЧЕСТВО ВЫПОЛНЕННЫХ УПРАЖНЕНИЙ */
    $modexCount = getModexCount($mysql, $uid, $modex, $tableName);
    $json = json_encode($modexCount);
    
    /* ВОЗВРАЩАЮ ИНФОРМАЦИЮ НА СТРАНИЦУ */
    echo $json;
    
    /* ЗАКРЫВАЮ СОЕДИНЕНИЕ С БАЗОЙ */
    closeConnection($mysql);
    
    //===================
    // МЕТОДЫ
    //===================
    
    function getUserData(&$uid, &$modex, &$stream){
        $uid = $_GET['uid'];
        $modex = $_GET['modex'];
        $stream = $_GET['stream'];
    }
    
    function openConnection(){
        $servername = "localhost";
        $username = "a0256806_mishkaJane";
        $password = "5u1edvlA";
        $dbname = "a0256806_mishkaJane";
        
        $mysql = new mysqli($servername, $username, $password, $dbname);
        
        if($mysql->connect_error) {
            echo "Connection failed: ".$mysql->connect_error;
        }
        return $mysql;
    }
    
    function closeConnection(&$mysql){
        $mysql->close();
    }
    
    function getModexCount(&$mysql, $uid, $modex, $tableName){
        $checkUser = checkInstanceInDB($mysql, $uid, $modex, $tableName);
        if($checkUser == 1){
            $countEx = getCountExFromDB($mysql ,$uid, $modex, $tableName);
            return [
            'success' => '1',
            'count_ex' => $countEx
            ];
        } else {
            return [
                'success' => '0'
            ];
        }
    }
    
    function checkInstanceInDB(&$mysql ,$uid, $modex, $tableName){
        $sqlSelect = "SELECT uid FROM $tableName WHERE uid = $uid AND modex = $modex";
        $result = $mysql->query($sqlSelect);
        if($result->num_rows > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
    function getCountExFromDB(&$mysql ,$uid, $modex, $tableName){
        $sqlSelect = "SELECT count FROM $tableName WHERE uid = $uid AND modex = $modex";
        $result = $mysql->query($sqlSelect);
        $row = $result->fetch_assoc();
        $countEx = $row["count"];
        return $countEx;
    }
?>