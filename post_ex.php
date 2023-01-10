<?php
    // регистрация выполнения упражнения
    // https://hmns.in/mishkajane/newVrs/post_ex.php?uid=333&modex=32&stream=5
    // response: 
    // success - ok
    // fail - max

    /* ОБЪЯВЛЯЮ ПЕРЕМЕННЫЕ */
    $uid;
    $modex;
    $stream;
    $tableName;
    $user = [];
    
    /* ПОЛУЧАЮ ДАННЫЕ ИЗ GET ЗАПРОСА */
    getUserData($uid, $modex, $stream);
    $tableName = 'users_'.$stream;
    
    /* ОТКРЫВАЮ СОЕДИНЕНИЕ С БАЗОЙ */
    $mysql = openConnection();
    
    /* РЕГИСТРИРУЮ ПОЛЬЗОВАТЕЛЯ В БД */
    $user = postUsersDataInDB($mysql, $uid, $modex, $tableName);
    $json = json_encode($user);

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
    
    function postUsersDataInDB(&$mysql, $uid, $modex, $tableName){
        $check = checkTableInDB($mysql, $tableName);
        
        if($check == 0) {
            createStreamTableInDB($mysql, $tableName);
        }
        
        $checkUser = checkInstanceInDB($mysql, $uid, $modex, $tableName);
        if($checkUser == 1){
            $oldCountEx = getCountExFromDB($mysql ,$uid, $modex, $tableName);
            if($oldCountEx < 5){
                updateUsersDataInDB($mysql, $uid, $modex, $tableName);
                $newCountEx = getCountExFromDB($mysql ,$uid, $modex, $tableName);
                return [
                'success' => '1',
                'user_status' => 'updated',
                'count_ex' => $newCountEx
                ];
            } else {
                return [
                'success' => '0',
                'user_status' => 'not updated',
                'reason' => 'exceeded max count_ex',
                'count_ex' => $oldCountEx
                ];
            }
        } else {
            addUsersDataInDB($mysql, $uid, $modex, $tableName);
            return [
                'success' => '1',
                'user_status' => 'added',
                'count_ex' => '1'
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
    
    function addUsersDataInDB(&$mysql, $uid, $modex, $tableName){
        $sqlInsertInto = "INSERT INTO $tableName(uid, modex, count) VALUES($uid, $modex, 1)";
        $mysql->query($sqlInsertInto);
    }
    
    function updateUsersDataInDB(&$mysql, $uid, $modex, $tableName){
        $sqlUpdate = "UPDATE $tableName SET count = count + 1 WHERE uid = $uid AND modex = $modex";
        $mysql->query($sqlUpdate);
    }
    
    function checkTableInDB(&$mysql, $tableName) {
        $dbname = "a0256806_mishkaJane";
        $tableName = "'{$tableName}'";
        
        $sqlCheckTable = "SHOW TABLES FROM $dbname LIKE $tableName";
        $result = $mysql->query($sqlCheckTable);
        
        if($result->num_rows > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
    function createStreamTableInDB(&$mysql, $tableName) {
        $sqlCreateTable = "CREATE TABLE IF NOT EXISTS $tableName ( id INT(255) NOT NULL AUTO_INCREMENT , uid INT(255) NOT NULL , modex INT(255) NOT NULL , count INT(255) NOT NULL , timestamp DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (id)) ENGINE = InnoDB";
        $result = $mysql->query($sqlCreateTable);
    }
?>