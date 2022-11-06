<?php
    $scaleid;
    $stage;
    
    getScaleid($scaleid);
    $mysql = openConnection();
    $stage = getStageFromDB($mysql, $scaleid);
    echo $stage;
    closeConnection($mysql);
    
    function getScaleid(&$scaleid){
        $scaleid = $_GET['scaleid'];
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
    
    function getStageFromDB(&$mysql, &$scaleid){
        $sqlSelect = "SELECT stage FROM scale WHERE scaleid = $scaleid";
        $result = $mysql->query($sqlSelect);
        $row = $result->fetch_assoc();
        $stage = $row["stage"];
        return $stage;
    }
?>