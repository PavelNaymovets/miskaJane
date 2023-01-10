<?php
    // запрос на все выполненные упражнения
    // https://hmns.in/mishkajane/newVrs/get_all.php?uid=333&stream=5
    // Если пользователя нет в самом начале, или нет вообще, прислать = 0;
    // response: 
    // {
    // 11:3,
    // 12:2,
    // 14:1,
    // ...
    // }

    /* ОБЪЯВЛЯЮ ПЕРЕМЕННЫЕ */
    $uid;
    $stream;
    $tableName;
    $modexCount = [];
    
    /* ПОЛУЧАЮ ДАННЫЕ ИЗ GET ЗАПРОСА */
    getUserData($uid, $stream);
    $tableName = 'users_'.$stream;
    
    /* ОТКРЫВАЮ СОЕДИНЕНИЕ С БАЗОЙ */
    $mysql = openConnection();
    
    /* ПОЛУЧАЮ КОЛИЧЕСТВО ПОВТОРЕНИЙ УПРАЖНЕНИЯ ПОЛЬЗОВАТЕЛЕМ */
    $modexCount = getModexCount($mysql, $uid, $tableName);
    $json = json_encode($modexCount);
    
    /* ВОЗВРАЩАЮ ИНФОРМАЦИЮ НА СТРАНИЦУ */
    echo $json;
    
    /* ЗАКРЫВАЮ СОЕДИНЕНИЕ С БАЗОЙ */
    closeConnection($mysql);
    
    function getUserData(&$uid, &$stream){
        $uid = $_GET['uid'];
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
    
    function getModexCount(&$mysql, $uid, $tableName){
        $checkUser = checkInstanceInDB($mysql, $uid, $tableName);
        if($checkUser == 1){
            $modexCount = getModexCountFromDB($mysql ,$uid, $tableName);
            return [
            'success' => '1',
            'moduleExercise_count' => $modexCount
            ];
        } else {
            return [
                'success' => '0'
            ];
        }
    }
    
    function checkInstanceInDB(&$mysql ,$uid, $tableName){
        $sqlSelect = "SELECT uid FROM $tableName WHERE uid = $uid";
        $result = $mysql->query($sqlSelect);
        if($result->num_rows > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
    function getModexCountFromDB(&$mysql ,$uid, $tableName){
        $sqlSelect = "SELECT modex, count FROM $tableName WHERE uid = $uid";
        $result = $mysql->query($sqlSelect);
        $modexCount = [];
        while($row = $result->fetch_assoc()) {
            $modexCount[$row["modex"]] = $row["count"];
          }
        return $modexCount;
    }
?>