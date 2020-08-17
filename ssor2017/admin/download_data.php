<?php

/******************************************************************

 申込みデータのダウンロードフォーム by Hirai 2015/07/07

******************************************************************/

/*---------------------------------------------------------------*/
/* 以下のdefineで始まる行に設定を記述してください.               */
/*                                                               */
/* PASSWORD: データのダウンロードに必要なパスワード.             */
/*                                                               */
/* CSV_PATH: 参加・発表申込みデータを蓄積するCSVファイルのパス.  */
/* 例："home/tsugu/public_html/anniversary/csv/namelist.csv"     */
/*                                                               */
/* CSV_PATH2: 論文投稿データを蓄積するCSVファイルのパス.         */
/* 例："home/tsugu/public_html/anniversary/csv/namelist2.csv"    */
/*                                                               */
/* また, 以下の$adminsに申込みデータのダウンロードを許可する方の */
/* メールアドレスを記述してください.                             */
/*---------------------------------------------------------------*/

// PHP(フォームの内容を処理する)
define("PASSWORD", "shioda2016");
define("CSV_PATH", "../csv/regist_data2016.csv");
define("CSV_PATH2", "../csv/submit_data2016.csv");

//日本語メールライブラリ読み込み
error_reporting(0); //エラー抑制
require("../lib/jphpmailer.php");

mb_language("japanese");
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

//ダウンロードを許可するメールアドレスのリスト
$admins = array(
	"kkatou@kanagawa-u.ac.jp",
	"m_kobayashi@tsc.u-tokai.ac.jp",
	"inoie@nw.kanagawa-it.ac.jp",
	"kou-f@mail.dendai.ac.jp",
	"sakuma@nda.ac.jp",
);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta name="robots" content="noindex,nofollow" />
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="content-script-type" content="text/javascript" />
<title>待ち行列シンポジウム 申込みデータのダウンロード</title>
<link rel="stylesheet" href="../css/style.css" type="text/css" />
</head>

<body>

<div id="container">

<h2 id="title">待ち行列シンポジウム 申込みデータのダウンロード</h2>

<hr>

<p>
メールアドレスとパスワードを入力後、送信ボタンをクリックしてください。<br>
申込みデータを保存したCSVファイルが送信されます。
</p>

<form method="post" action="">
	メールアドレス：<input type="text" name="email" value="" />
	パスワード：<input type="password" name="password" value="" />
	<input type="submit" name="action" value="送信" />
</form>

</body>

<?php

// メインスクリプトここから

if(isset($_POST["action"]) && $_POST["action"]==="送信"){
	if(PASSWORD === $_POST["password"]){
		$is_admin = false;
		$mail_to;

		foreach ($admins as $admin) {
			if($admin === $_POST["email"]){
				$is_admin = true;
				$mail_to = $admin;
			}
		}

		if($is_admin){
			send_csv($mail_to);
		}else{
			echo("<p>メールアドレスが正しくありません</p>");
		}

	}else{
		echo("<p>パスワードが正しくありません</p>");
	}
}

function send_csv($to) { //宛先
    //日本語添付メールを送る
    $subject = "待ち行列シンポジウム 申込みデータ"; //題名
    $body = "申込みデータを保存したCSVファイルが添付されています。"; //本文
    $from = "queue_inquiry@googlegroups.com"; //差出人
    $fromname = "待ち行列シンポジウム実行委員会"; //差し出し人名
    $attachfile = CSV_PATH;
    $attachfile2 = CSV_PATH2;

    $mail = new JPHPMailer();
    $mail->addTo($to);
    $mail->setFrom($from,$fromname);
    $mail->setSubject($subject);
    $mail->setBody($body);
    $mail->addAttachment($attachfile);
    $mail->addAttachment($attachfile2);

    if (!$mail->send()){
        echo("<p>申込みデータの送信に失敗しました：".$mail->getErrorMessage()."</p>");
    }else{
        echo("<p>申込みデータの送信が完了しました</p>");
    }

}

// メインスクリプトここまで

?>

</html>
