<?php

    include "./dbconnection.php";

    $showLog = false;

    $post_url = "https://jsonplaceholder.typicode.com/posts";
    $comments_url = "https://jsonplaceholder.typicode.com/comments";


    function GetRequest($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);
    
        return json_decode($html,true);
    };

    $mysqli = new mysqli($host, $user, $pass, $dbname);

    /* check connection */ 
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    
    //printf("Host information: %s\n", $mysqli->host_info);
    
    $posts = GetRequest($post_url);
    $postsCount = count($posts);

    $comments = GetRequest($comments_url);
    $commentsCount = count($comments);

    $query_base = "INSERT INTO " . $dbname .".posts (`post_id`, `user_id`, `title`, `post_body`) VALUES ";
    
    foreach($posts as $key => $val){
        $query = $query_base;
        $query .= "(" . $val['id'] . "," . $val["userId"] . ",'" 
            . $mysqli->real_escape_string($val["title"]) . "','" 
            . $mysqli->real_escape_string($val["body"]) . "')";

        $queryResult = $mysqli->query($query);
        if ($showLog) {
            if ($queryResult === true) {
                echo "posts: new record " . $val["id"] . " created successfully\n";
            } else {
                echo "Error: " . $query . "\n" . $mysqli->error ."\n";
            };
        };
    }; 

    $query_base = "INSERT INTO " . $dbname .".comments "
        . "(`id`, `post_id`, `name`, `email`, `body`) VALUES ";

    foreach($comments as $key => $val){
        $query = $query_base;
        $query .= "(" . $val['id'] . "," . $val["postId"] . ",'" 
            . $mysqli->real_escape_string($val["name"]) . "','" 
            . $mysqli->real_escape_string($val["email"]). "','" 
            . $mysqli->real_escape_string($val["body"]) . "')";

        $queryResult = $mysqli->query($query);
        if ($showLog) {
            if ($queryResult === true) {
                echo "comments: new record " . $val["id"] . " created successfully\n";
            } else {
                echo "Error: " . $query . "\n" . $mysqli->error ."\n";
            };
        };
    };


    printf("Загружено %d записей и %d комментариев\n",$postsCount,$commentsCount);

    /* close connection */
    $mysqli->close();

?>