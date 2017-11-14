<?php
  include './config.php';
  session_start();
  $userId = $_SESSION['id'];
  $userId = (integer)$userId;

  if($_SESSION["user"] === NULL){
?> 
    <a href='./register.php'>Войдите на сайт</a>
<?php
    exit();
  } 
  $users = $_SESSION["user"];
  echo "<h1>Здравствуйте, $users! Вот ваш список дел:</h1>";

// подключение к db
  try {
    $pdo = new PDO(
    	'mysql:host=localhost;dbname=global',
    	$user,
    	$password,
    	[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
  }
  catch (PDOException $e) {
    echo "Невозможно установить соединение с базой данных";
    exit();
  }


  $idEdit = $_GET["id"];
  $idEdit = intval($idEdit);
// Удалить
  if($_GET["action"] ==='delete'){
    $queryEdit = $pdo->prepare("DELETE FROM `task` WHERE `id` = ? ");
    $paramsEdit = [$idEdit];
    $queryEdit->execute($paramsEdit);
   }
// Выполнить
  if($_GET["action"] ==='done'){
    $queryEdit = $pdo->prepare("UPDATE `task`SET `is_done`='1' WHERE `id` = ?" );
    $paramsEdit = [$idEdit];
    $queryEdit->execute($paramsEdit);
  }
//добавить 
  if(count($_POST > 0)){
    $description = trim($_POST['description']);
    $description = htmlspecialchars($description);
    $is_done = 0;
    $is_done = intval($is_done);

    if($description != ''){
      $query = $pdo->prepare('INSERT INTO `task` (`id`,`user_id`,`assigned_user_id`, `description`, `is_done`, `date_added`) VALUES (NULL,"'.$userId.'", "'.$userId.'",?, ?, CURRENT_TIMESTAMP)');
      $params = [$description, $is_done];
      $query->execute($params);
    }
  }
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <title>Запросы SELECT, INSERT, UPDATE и DELETE</title>
    <style>
      table { 
        border-spacing: 0;
        border-collapse: collapse;
      }

      table td, table th {
        border: 1px solid #ccc;
        padding: 5px;
      }

      table th {
        background: #eee;
      }
    </style>
  </head>
  <body>
<?php
    $sql2 = 'SELECT login, id FROM user';
    $statements = $pdo->prepare($sql2);
    $statements->execute();

    while ($rows = $statements->fetch(PDO::FETCH_ASSOC)) {
      $results[] = $rows;
    }
?>
    <form method="POST">
      <input type="text" name="description" placeholder="Описание задачи" value="">
      <input type="submit" name="save" value="Добавить">
    </form>

    <table>
      <tr>
        <th>Описание задачи</th>
        <th>Дата добавления</th>
        <th>Статус</th>
        <th>Изменить</th>
        <th>Ответственный</th>
        <th>Автор</th>
        <th>Закрепить задачу за пользователем</th>
      </tr>
<?php
      $sql = 'SELECT user.id AS user_id, user.login AS user_login, task.id AS task_id, task.user_id AS task_user_id, task.assigned_user_id AS task_assigned_user_id, task.description, task.is_done, task.date_added FROM user LEFT JOIN task ON user.id=task.assigned_user_id WHERE task.user_id = "'.$userId.'"';
      $statement = $pdo->prepare($sql);
      $statement->execute();

      while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
      }

      foreach ($result as $key => $value) {
        $task_id = $value["task_id"];
        $id = $value["task_assigned_user_id"];
        $id = intval($id);
        if($value['is_done'] == 0){
          $is_done = 'В процессе';
        } else{
          $is_done = 'Выполнено';
        }

?>
        <tr>
          <td><?=$value['description'];?></td>
          <td><?=$value['date_added'];?></td>
          <td><?=$is_done;?></td>
          <td><a href="?id=<?="$task_id";?>&action=done">Выполнить</a> <a href="?id=<?=$task_id;?>&action=delete">Удалить</a></td>
          <td><?=$value['user_login'];?></td>
          <td><?=$users;?></td>
          <td>
            <form method='POST'>
              <select name='assigned_user_id[]'>
<?php
              foreach ($results as $keys => $values) {
                $idsss = $values['id'];
                $arrays = array("$idsss", "$task_id", "5545");
?>
                <option value="<?=$values['id'];?>"> <?=$values['login'];?>,<?=$values['id'];?></option>
<?php  
              }
?>
              </select>
              <input type='submit' name='assign' value='Переложить ответственность'>
            </form>
          </td>
        </tr>
<?php
      }
      if(!empty($_POST["assigned_user_id"])  ){
      	$assigned_user_id = $_POST["assigned_user_id"];
      	$task_id = $_POST["assign"];
      	$queryEdit = $pdo->prepare("UPDATE `task`SET `assigned_user_id`=? WHERE `id` = ?" );
      	$paramsEdit = [$assigned_user_id,$task_id];
      	$queryEdit->execute($paramsEdit);
      }
?>
    </table>

    <p><strong>Также, посмотрите, что от Вас требуют другие люди:</strong></p>

    <table>
      <tr>
        <th>Описание задачи</th>
        <th>Дата добавления</th>
        <th>Статус</th>
        <th>Изменить</th>
        <th>Ответственный</th>
        <th>Автор</th>
      </tr>

<?php
//Переложить ответственность
      $sql_assign = 'SELECT user.id AS user_id, user.login AS user_login, task.id AS task_id, task.user_id AS task_user_id, task.assigned_user_id AS task_assigned_user_id, task.description, task.is_done, task.date_added FROM user LEFT JOIN task ON user.id = task.user_id WHERE task.assigned_user_id= "'.$userId.'" AND task.user_id<>"'.$userId.'"';
      $statement_assign = $pdo->prepare($sql_assign);
      $statement_assign->execute();

      while ($row_assign = $statement_assign->fetch(PDO::FETCH_ASSOC)) {
      	$result_assign[] = $row_assign;
      }

      foreach ($result_assign as $key_assign => $value_assign) {
      	$task_id_assign = $value_assign['task_id'];
      	if($value_assign['is_done'] == 0){
      	  $is_done = 'В процессе';
      	} else{
      	  $is_done = 'Выполнено';
      	}
?>
        <tr>
          <td><?=$value_assign['description'];?></td>
          <td><?=$value_assign['date_added'];?></td>
          <td><?=$is_done;?></td>
          <td><a href="?id=<?="$task_id_assign";?>&action=done">Выполнить</a></td>
          <td><?=$users?></td>
          <td><?=$value_assign['user_login'];?></td>
        </tr>
<?php
      }
?>
    </table>
    <p><a href="./logout.php">Выход</a></p>
  </body>
</html>
