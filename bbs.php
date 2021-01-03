<?php
//


// データベースに接続する
// if (接続成功？) {
//     if (リクエストメソッドがPOST？) {
//         フォームからのデータ受け取り処理
//         エラーチェック処理
//         if (エラーが一切ないか？) {
//             登録処理（INSERT INTO）
//         }
//     }
    
//     表示のためのデータ取得処理（SELECT）
// } else {
//     接続エラー
// }

// データベースから切断する

// 表示処理
// <!DOCTYPE html>
// ....


$success_message = '';
$name = '';
$comment = '';
$error = array();

$host = 'localhost'; // データベースのホスト名又はIPアドレス ※CodeCampでは「localhost」で接続できます
$username = 'root';  // MySQLのユーザ名
$passwd   = 'root';    // MySQLのパスワード
$dbname   = 'bbs';    // データベース名
$link = mysqli_connect($host, $username, $passwd, $dbname);
// 接続成功した場合
if ($link) {
    // 文字化け防止
    mysqli_set_charset($link, 'utf8');
    //フォームからのデータ受け取り
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
        if(isset($_POST['my_name']) === TRUE){
            $name = $_POST['my_name'];
        }
        if(isset($_POST['comment']) === TRUE){
            $comment = $_POST['comment'];
        }
        //エラーチェック
        if($name === ''){
            $error[]='名前を入力してください';
        }
        if(mb_strlen($name) > 20){
            $error[]='名前は20字以内で入力してください';
        }
          if($comment === ''){
            $error[]='コメントを入力してください';
        }
        if(mb_strlen($comment) > 100){
            $error[]='コメントは100字以内で入力してください';
        }
        //エラーがなかったら登録
        if(empty($error) === true){
            $sql = 'INSERT INTO bbs_table' . PHP_EOL
                 . '(bbs_name, bbs_comment, bbs_date)' . PHP_EOL
                 . 'VALUES' . PHP_EOL
                 . '(' . "'" . $name . "'" . ',' . "'" . $comment . "'" . ', NOW())';
        //結果を表示
            $result = mysqli_query($link, $sql);
            if ($result === TRUE) {
                $success_message = 'メッセージを登録しました';
            } else {
                $error[] = '登録に失敗しました';
            }
        }
    }
    
    $data = [];
    //表示のためのデータを取得
    $sql = 'SELECT bbs_id, bbs_name, bbs_comment, bbs_date FROM bbs_table ORDER BY bbs_date DESC';
    $result = mysqli_query($link, $sql);
    if ($result !== FALSE) {
        while ($row = mysqli_fetch_array($result)) {
            $data[] = $row; // ['bbs_id' => 10, 'bbs_name' => 'yamada'...]
        }
        mysqli_free_result($result);
    } else {
        $error[] = 'SQLエラー : ' . $sql . ' => ' . mysqli_error($link);
    }

    // DB切断
    mysqli_close($link);
} else {
    $error[] = 'DB接続に失敗しました';
}

?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>ひとこと掲示板</title>
    </head>
    <body>
        <h1>ひとこと掲示板</h1>
        <!---登録完了メッセージの表示--->
<?php if ($success_message !== '') { ?>
        <p><?php print $success_message; ?></p>
<?php } ?>
        <!---エラーメッセージの表示--->
        <?php foreach($error as $value) { ?>
        <p><?php print $value; ?></p>
        <?php } ?>

        <form method="post">
        <style> 
        p {
            margin: 0;
        }
        </style>
            <p>名前：</p><input type="text" name="my_name" size="40" placeholder="お名前を入力してください"><br>
            <p>発言：</p><textarea name="comment" cols="40" rows="5" maxlength="100" placeholder="100字以内でコメントを入力してください"></textarea><br>
            <input type="submit" name="submit" value="投稿">
        </form></br>
        <p>投稿一覧</p>
        <!---$dataの配列を展開してエスケープ処理、表示--->
        <?php foreach ($data as $read) { ?>
            <p><?php print htmlspecialchars($read['bbs_name'] . ' : ' . $read['bbs_comment'] . ' - ' . $read['bbs_date'], ENT_QUOTES, 'utf-8'); ?></p>
        <?php } ?>
    </body>
</html>