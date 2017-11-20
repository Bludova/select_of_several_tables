<?php
session_start();
include './config.php';
//подключение к bd
try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8",
        $user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
}
catch (PDOException $e) {
    echo "Невозможно установить соединение с базой данных";
    exit();
}

//регистрация
if(isset($_POST["register"])) {
    if(empty($_POST['login']))
        $err[] = 'Не введен Логин';

    if(empty($_POST['password']))
        $err[] = 'Не введен Пароль';
    if(count($err) > 0)
        echo $err[0].'<br>';

    if(count($err) === 0){
        $password = trim($_POST["password"]);
        $login = trim($_POST["login"]);
        ?>
        <h1>Вы успешно зарегистрировались!</h1>
        <p>Данные вашего аккаунта:<br>
          Логин (username): <?=$login;?><br>
          Пароль (password): <?=$password;?><br>
        </p>
        <?php
        $login = htmlspecialchars($login);
        $password = htmlspecialchars($password);
        $password = md5($password);
        $sql = $pdo->prepare("INSERT INTO `user` (`id`, `login`, `password`) VALUES (NULL, ?, ?)");
        $params = [$login, $password];
        $sql->execute($params);
        $_SESSION['user'] = $login;
        $_SESSION['id'] = $userId;
    }
}
// Вход
if(isset($_POST["sign_in"])){
    if($_POST["password"] != '' and $_POST["login"] != ''){
        $password = trim($_POST["password"]);
        $login = trim($_POST["login"]);
        $login = htmlspecialchars($login);
        $password = htmlspecialchars($password);
        $password = md5($password);
        $query = $pdo->prepare("SELECT * FROM `user` WHERE login = ? AND password = ?");
        $params = [$login, $password];
        $query->execute($params);
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $userId = $row['id'];

        if ($row !== false){
            $_SESSION['user'] = $login;
            $_SESSION['id'] = $userId;
            header("Location: ./index.php",TRUE,302);
            exit();
        }else {
            echo "Не правельный логин или пароль!";
        }
    }
}
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
</body>
</html>
