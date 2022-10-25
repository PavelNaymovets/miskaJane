<?php
    // запрос на все выполненные упражнения
    // https://hmns.in/mishkajane/get_all.php?uid=333
    // Если пользователя нет в самом начале, или нет вообще, прислать = 0;
    // response: 
    // {
    // 11:3,
    // 12:2,
    // 14:1,
    // ...
    // }

    $uid;
    $modexCount = [];
    getUserData($uid);
    $mysql = openConnection();
    $modexCount = getModexCount($mysql, $uid);
    $json = json_encode($modexCount);
    echo $json;
    closeConnection($mysql);
    
    function getUserData(&$uid){
        $uid = $_GET['uid'];
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
    
    function getModexCount(&$mysql, $uid){
        $checkUser = checkInstanceInDB($mysql, $uid);
        if($checkUser == 1){
            $modexCount = getModexCountFromDB($mysql ,$uid);
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
    
    function checkInstanceInDB(&$mysql ,$uid){
        $sqlSelect = "SELECT uid FROM users WHERE uid = $uid";
        $result = $mysql->query($sqlSelect);
        if($result->num_rows > 0) {
            return 1;
        } else {
            return 0;
        }
    }
    
    function getModexCountFromDB(&$mysql ,$uid){
        $sqlSelect = "SELECT modex, count FROM users WHERE uid = $uid";
        $result = $mysql->query($sqlSelect);
        $modexCount = [];
        while($row = $result->fetch_assoc()) {
            $modexCount[$row["modex"]] = $row["count"];
          }
        return $modexCount;
    }
?>