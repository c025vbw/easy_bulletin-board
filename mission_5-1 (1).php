<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>簡易掲示板</title>
</head>
<body>
<?php
    //データベースの作成
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザーネーム';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    // デーブル確認用
    // $sql ='SHOW TABLES';
    // $result = $pdo -> query($sql);
    // foreach ($result as $row){
    //     echo $row[0];
    //     echo '<br>';
    // }
    // echo "<hr>";
    // テーブル削除用
    // $sql = 'DROP TABLE tbtest';
    // $stmt = $pdo->query($sql);
    $sql = "CREATE TABLE IF NOT EXISTS tbtest"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "pass TEXT"
    .");";
    $stmt = $pdo->query($sql);
    
    $fr_name = "";
    $txt = "";
    $edit = "";
    if(!empty($_POST["str"]) && !empty($_POST["komento"])){
        if(!empty($_POST["edit_name"])){
            $id = $_POST["edit_name"]; //変更する投稿番号
            $name = $_POST["str"];
            $comment = $_POST["komento"]; //変更したい名前、変更したいコメントは自分で決めること
            $sql = 'UPDATE tbtest SET name=:name,comment=:comment WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }elseif(!empty($_POST["pass"])){
            $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, pass) VALUES (:name, :comment, :pass)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
            $name = $_POST["str"];
            $comment = $_POST["komento"];//好きな名前、好きな言葉は自分で決めること
            $pass = $_POST["pass"];
            $sql -> execute();
        }
    }elseif (!empty($_POST["del"]) && (!empty($_POST["del_pass"]))){//削除用
        $id = $_POST["del"];
        $sql = 'SELECT * FROM tbtest WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();  
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if(($_POST["del_pass"]) == $row['pass']){
                $sql = 'delete from tbtest where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }elseif(!empty($_POST["edit_kaku"]) && (!empty($_POST["edit_pass"]))){//編集用
        $id = $_POST["edit_kaku"];
        $sql = 'SELECT * FROM tbtest WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();  
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            if(($_POST["edit_pass"]) == $row['pass']){
                $fr_name = $row['name'];
                $txt = $row['comment'];
                $edit = $id;
            }
        }
    }
    // 中身表示
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        // 確認用に表示する
        echo $row['pass'];
    echo "<hr>";
    }
?>
<form action="" method="post">
        <input type="text" name="str" value = "<?php echo $fr_name; ?>" placeholder="名前" >
        <input type="text" name="komento"  value = "<?php echo $txt; ?>" placeholder="コメント" >
        <input type="text" name="pass" placeholder="パスワード" >
        <input type="submit" name="submit"><br>
        <input type="text" name="del"  placeholder="削除番号指定用フォーム" >
        <input type="text" name="del_pass" placeholder="パスワード" >
        <input type="submit" name="del_sub" value = "削除"><br>
        <input type="text" name="edit_kaku"  placeholder="編集番号指定用フォーム" >
        <input type="text" name="edit_pass" placeholder="パスワード" >
        <input type="submit" name="edit_sub" value = "内容表示"><br>
        <input type ="text" name = "edit_name" value = "<?php echo $edit; ?>">
    </form>
</body>
</html>