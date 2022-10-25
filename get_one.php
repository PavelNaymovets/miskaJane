<?php
    // запрос на конкретное упражнение
    // https://hmns.in/mishkajane/get_one.php?uid=333&modex=11
    // Первая цифра - номер тренинга, вторая - номер упражнения
    // Если пользователя нет, то шли 0;
    // response: 
    // [1:5]
    // example: 2
    
    $uid;
    $modex;
    $modexCount = [];
    getUserData($uid, $modex);
    $mysql = openConnection();
    $modexCount = getModexCount($mysql, $uid, $modex);
    $json = json_encode($modexCount);
    echo $json;
    closeConnection($mysql);
    
    function getUserData(&$uid, &$modex){
        $uid = $_GET['uid'];
        $modex = $_GET['modex'];
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
    
    function getModexCount(&$mysql, $uid, $modex){
        $checkUser = checkInstanceInDB($mysql, $uid, $modex);
        if($checkUser == 1){
            $countEx = getCountExFromDB($mysql ,$uid, $modex);
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
    
    function checkInstanceInDB(&$mysql ,$uid, $modex){
        $sqlSelect = "SELECT uid FROM users WHERE uid = $uid AND modex = $modex";
        $result = $mysql->query($sqlSelect);
        if($result->num_rows > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
    function getCountExFromDB(&$mysql ,$uid, $modex){
        $sqlSelect = "SELECT count FROM users WHERE uid = $uid AND modex = $modex";
        $result = $mysql->query($sqlSelect);
        $row = $result->fetch_assoc();
        $countEx = $row["count"];
        return $countEx;
    }
?>