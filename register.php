<?php
session_start();
//SELECT book.name, book.year,book.isbn, book_autor.name as `autor` FROM book JOIN book_author ON book_author.id=book.author_id

// SELECT task.id, task.user_id, task.assigned_user_id, task.description, user.id as `autor` FROM task JOIN user ON task.user_id=user.id

// SELECT task.id, task.user_id, task.assigned_user_id, task.description, user.id as `autor` FROM task JOIN user ON task.user_id=user.id
// SELECT * FROM task JOIN user ON task.user_id=user.id
// SELECT * FROM task JOIN user ON task.user_id=user.id
 // $_SESSION['user'] = $user;
 //  $_SESSION['password'] = $password;
// require_once __DIR__ . '/core/functions.php';
// $currentUser = getCurrentUser();
// if (!$currentUser && !$_POST['guest']) {
//     redirect('login');
// }

  include './config.php';
 //header("Location: ./list.php",TRUE,302);
// подключение к db
      try {
        $pdo = new PDO(
        "mysql:host=$host;dbname=$db",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
      }
      catch (PDOException $e) {
        echo "Невозможно установить соединение с базой данных";
        exit();
      }

//регистрация
      if(@$_POST["register"]) {

      if(empty($_POST['login']))
	    $err[] = 'Не введен Логин';
	
	if(empty($_POST['password']))
	  $err[] = 'Не введен Пароль';
	if(count($err) > 0)
	  echo $err[0].'<br>';

      	if(count($err) === 0){
      		$password = trim($_POST["password"]);
      		$login = trim($_POST["login"]);
            $login = htmlspecialchars($login);
            $password = htmlspecialchars($password);
            $password = md5($password);
            $query = $pdo->prepare("INSERT INTO `user` (`id`, `login`, `password`) VALUES (NULL, ?, ?)");
            $params = [$login, $password];
            $query->execute($params);
            $_SESSION['user'] = $login;
  // $_SESSION['password'] = $password;
          //  header("Location: ./index.php");
            header("Location: ./index.php",TRUE,302);
            exit();
      	 }//else {
      	// 	echo 'Введите логин и пароль для регистрации!';
      	// }
}
// Вход

if(@$_POST["sign_in"]){
	if($_POST["password"] != '' and $_POST["login"] != ''){
		$password = trim($_POST["password"]);
      		$login = trim($_POST["login"]);
            $login = htmlspecialchars($login);
            $password = htmlspecialchars($password);
            $password = md5($password);
            $query = $pdo->prepare("SELECT * FROM `user` WHERE `login` = ? AND `password` = ?");
            //AND `password` = ?
            //  $sql2 = "SELECT * FROM `books` WHERE `isbn` LIKE  ? AND `name` LIKE ? AND `author` LIKE ? ";
            // $query = $pdo->prepare("INSERT INTO `user` (`id`, `login`, `password`) VALUES (NULL, ?, ?)");
           $params = [$login, $password];
            $query->execute($params);
           //var_dump($query);
            // echo "привет $login";
            // echo "привет $password";
// $row = mysql_fetch_assoc($res);

            //Вы можете использовать (PDO::FETCH_ASSOC)постоянное 
//использование будет  
            $row = $query->fetch(PDO::FETCH_ASSOC);
            $userId = $row['id'];
// while ($row = $query->fetch(PDO::FETCH_ASSOC)){
// //....
// $testLog = $row['login'];
// }
// if(md5(md5($_POST['password']).$row['salt']) == $row['password'])
// 			{	
              //$count = exec($query);
//echo $testLog;
              //var_dump($row['login']);
  if ($row !== false){
                $_SESSION['user'] = $login;
                 $_SESSION['id'] = $userId;
            header("Location: ./index.php",TRUE,302);
            exit();


  	// header("Location: index.php");
            // exit();
    //echo "Привет $login";
  }else {
    echo "Не правельный логин или пароль!";
    // echo "<pre>";
    // print_r($pdo->errorInfo());
    // echo "</pre>";
      }
    }
  }

      //var_dump($_POST["sign_in"]);
      //array(3) { ["login"]=> string(3) "dbf" ["password"]=> string(3) "bdf" ["sign_in"]=> string(8) "Вход" }
      //array(3) { ["login"]=> string(3) "ggg" ["password"]=> string(3) "ggg" ["register"]=> string(22) "Регистрация" }
   // if(count($_POST > 0)){
   //        $description = trim($_POST['description']);
   //        $description = htmlspecialchars($description);
   //        $is_done = 0;
   //        $is_done = intval($is_done);

   //        if($description != ''){
   //          $query = $pdo->prepare("INSERT INTO `tasks` (`id`, `description`, `is_done`, `date_added`) VALUES (NULL, ?, ?, CURRENT_TIMESTAMP)");
   //          $params = [$description, $is_done];
   //          $query->execute($params);
   //          //header("Location: index.php");
   //          //exit();
   //        }
   //      }
?>
<!DOCTYPE html>
<html lang="ru">
  <head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
  </head>
  <body>
    <p>Введите данные для регистрации или войдите, если уже регистрировались:</p>

<form method="POST">
    <input type="text" name="login" placeholder="Логин">
    <input type="password" name="password" placeholder="Пароль">
    <input type="submit" name="sign_in" value="Вход">
    <input type="submit" name="register" value="Регистрация">
</form>