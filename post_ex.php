<?php
    // регистрация выполнения упражнения
    // https://hmns.in/mishkajane/post_ex.php?uid=1345&modex=12
    // response: 
    // success - ok
    // fail - max
    
    $uid;
    $modex;
    $user = [];
    getUserData($uid, $modex);
    $mysql = openConnection();
    $user = postUsersDataInDB($mysql, $uid, $modex);
    $json = json_encode($user);
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
    
    function postUsersDataInDB(&$mysql, $uid, $modex){
        $checkUser = checkInstanceInDB($mysql, $uid, $modex);
        if($checkUser == 1){
            $oldCountEx = getCountExFromDB($mysql ,$uid, $modex);
            if($oldCountEx < 5){
                updateUsersDataInDB($mysql, $uid, $modex);
                $newCountEx = getCountExFromDB($mysql ,$uid, $modex);
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
            addUsersDataInDB($mysql, $uid, $modex);
            return [
                'success' => '1',
                'user_status' => 'added',
                'count_ex' => '1'
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
    
    function addUsersDataInDB(&$mysql, $uid, $modex){
        $sqlInsertInto = "INSERT INTO users(uid, modex, count) VALUES($uid, $modex, 1)";
        $mysql->query($sqlInsertInto);
    }
    
    function updateUsersDataInDB(&$mysql, $uid, $modex){
        $sqlUpdate = "UPDATE users SET count = count + 1 WHERE uid = $uid AND modex = $modex";
        $mysql->query($sqlUpdate);
    }
?>