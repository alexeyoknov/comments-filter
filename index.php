<html lang="en">
  <head>
    <title>Поиск в комментариях</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  </head>
  <body>
    <div class="container panel panel-default ">
      <h2 class="panel-heading">Поиск в комментариях</h2>
      <form id="newSearchForm" method=POST action="./index.php">
        <div class="form-group">
          <label for="search">Искомый текст:</label>
          <input type="text" name="search" class="form-control"
            <?
                 if (isset($_POST["search"]))
                    echo " value='" . $_POST["search"] ."' ";
            ?>placeholder="Строка поиска" id="search">
        </div>

        <div class="form-group">
          <button class="btn btn-default" id="submit" type=submit>Найти</button>
        </div>
      </form>
    </div>
<?
    include "./dbconnection.php";
    if (isset($_POST["search"])&& strlen($_POST["search"])<3)
    {
        ?>
        <div class="alert alert-danger" role="alert">
            Короткий текст для поиска (менее 3 символов)!
        </div>
        <?
        exit();
    } else {
        
        $mysqli = new mysqli($host, $user, $pass, $dbname);

        /* check connection */ 
        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        };

        $query = "SELECT title, post_id, body FROM " . $dbname . ".comments "
            . "JOIN ". $dbname . ".posts USING(post_id) "
            . "WHERE body LIKE '%" . $_POST["search"] . "%'";

        $queryResult = $mysqli->query($query);
        if ($queryResult === false) {
            echo "Error: " . $query . "\n" . $mysqli->error ."\n";
            exit();
        };
        ?>
        <div class="container panel panel-default ">
        <table class="table table-striped table-hover">
            <thead>
                <?
                $tableHeader = ['Заголовок поста','Комментарий','Ссылка на комментарий'];
                foreach($tableHeader as $th){
                    echo "<th>" . $th . "</th>";
                };
                ?>
            </thead>
            <tbody>
                <?
                while($ans=$queryResult->fetch_object())
                {
                ?>
                <tr>
                    <td> <? echo $ans->title; ?> </td>
                    <td> <? echo $ans->body; ?> </td>
                    <td><a href="https://jsonplaceholder.typicode.com/posts/<?
                            echo $ans->post_id;
                        ?>/comments">Подробно</a></td>
                </tr>
                <?};?>
            </tbody>
        </table>
        </div>
<?

    };
?>
 </body>
</html>