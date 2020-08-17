<?php

/******************************************************************

 2017 SSOR アブストラクト投稿フォーム 以下のフォームを改変
 待ち行列シンポジウム 論文投稿フォーム by Hirai 2015/07/07

******************************************************************/

/*---------------------------------------------------------------*/
/* 以下のdefineで始まる行に設定を記述してください.               */
/*                                                               */
/* CSV_PATH: 名簿データを蓄積するCSVファイルのパス.              */
/*           親ディレクトリが存在すること.                       */
/* 例："home/tsugu/public_html/anniversary/csv/namelist.csv"     */
/*                                                               */
/* TMP_DIR: アップロードされた論文を一時保存するディレクトリ.    */
/*          存在して最後はスラッシュで終わること.                */
/* 例："home/tsugu/public_html/anniversary/tmp/"                 */
/*                                                               */
/* PDF_DIR: アップロードされた論文を保存するディレクトリ.        */
/*          存在して最後はスラッシュで終わること.                */
/* 例："home/tsugu/public_html/anniversary/pdf/"                 */
/*                                                               */
/* ※パーミッションについて                                      */
/* ファイルはApacheのユーザー権限nobodyで作成されるので,         */
/* このユーザーが書き込めるように, 全ての親ディレクトリについて  */
/* xの権限があって, 直近の親ディレクトリについてrwxの権限が必要. */
/* ファイルを修正した場合は, ファイル自身の権限が変更されます.   */
/* 修正の後にrwxの権限を持つよう必ず変更し直して下さい.          */
/* 変更を怠った場合, 修正以降のデータ書き込みは行われません.     */
/*                                                               */
/* 上の例では                                                    */
/* /home/tsugu/public_html/anniversary/ 701                      */
/* /home/tsugu/public_html/anniversary/csv/ 707                  */
/* /home/tsugu/public_html/anniversary/csv/namelist.csv 707      */
/* /home/tsugu/public_html/anniversary/tmp/ 707                  */
/* /home/tsugu/public_html/anniversary/pdf/ 707                  */
/*---------------------------------------------------------------*/

// PHP(フォームの内容を処理する)
define("CSV_PATH", "./csv/submit_data2017.csv");
define("TMP_DIR", "./tmp/");
define("PDF_DIR", "./pdf/");

//日本語メールライブラリ読み込み
error_reporting(0); //エラー抑制
require("./lib/jphpmailer.php");

mb_language("japanese");
mb_internal_encoding("UTF-8");
mb_regex_encoding("UTF-8");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta name="robots" content="noindex,nofollow" />
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<meta http-equiv="content-style-type" content="text/css" />
<meta http-equiv="content-script-type" content="text/javascript" />

<title>2017年 中国・四国地区ＳＳＯＲ アブストラクト投稿</title>
<link rel="stylesheet" href="./css/style.css" type="text/css" />
</head>

<body>

<div id="container">

<h2 id="title">2017年 中国・四国地区ＳＳＯＲ  アブストラクト投稿</h2>

<hr>

<?php

// メインスクリプトここから

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // POSTの時
    $form = new Form($_POST);
    if ($form->data["act"] == "ent")
    {
        // 入力画面へのPOSTの時
        scr_ent();
    }
    else if ($form->data["act"] == "conf")
    {
        // 確認画面へのPOSTの時
        if ($form->is_valid())
        {
            scr_conf();
        }else{
            scr_ent();
        }
    }else if ($form->data["act"] == "sub"){
        // 送信画面へのPOSTの時
        if ($form->is_valid())
        {
            $form->save();
            send_email($form);
            scr_sub();
        }else{ //不正アクセス対策
            $form = new Form();
            scr_ent();
        }
    }

}else{
    $form = new Form();
    scr_ent();
}

// メインスクリプトここまで

?>

<p>
<!-- <a href="https://sites.google.com/site/qsymp2016/">トップページへ戻る</a> -->
<a href="http://www.orsj.or.jp/chu_shikoku/ssor2017/index.html">トップページへ戻る</a>
</p>

</div>

</body>

</html>

<?php

abstract class BaseForm
{
    function __construct($data = null)
    {
        $this->data = $data;
        $this->errors = array();
    }

    // $nameで指定したフィールドが空白の時Trueを返す
    protected function is_empty($name)
    {
        return !array_key_exists($name, $this->data) || $this->data[$name] == "";
    }

    // $nameで指定したフィールドに対してエラーメッセージが作成されている時エラーメッセージをHTMLタグ付きで返す
    public function error_message($name)
    {
        if (isset($this->errors[$name]))
        {
            $message = "<em>".$this->errors[$name]."</em>";
        }else{
            $message = "";
        }

        return $message;
    }

    abstract public function save();
}

//関数is_validで例外が発生した時に呼び出される例外クラスを定義
class ValidationException extends Exception
{
}

class Form extends BaseForm
{
    public $fields;
    public $tmp_paper_name;
    public $pdf_paper_name;

    function __construct($data = null)
    {
        parent::__construct($data);

        $this->fields = array(
		"last_name" => array("required" => true), //入力必須
		"first_name" => array("required" => true),
		"last_name_ruby" => array("required" => true),
		"first_name_ruby" => array("required" => true),
		"affiliation" => array("required" => true),
		"email" => array("required" => true),
		"email_retype" => array("required" => true),
		"attend_type" => array("required" => true),
		"presentation_type" => array("required" => true),
		"notes" => array("required" => false),
        );
    }

    //全てのフォームが正しく入力されている時Trueを返す
    public function is_valid()
    {
        //文字列入力に関する確認
        foreach ($this->fields as $name => $field)
        {
            $required = $field["required"];

            if ($required && $this->is_empty($name))
            {
                //必須項目が入力されていない時
                $this->errors[$name] = "入力してください";
            }
            else if (method_exists($this, "clean_".$name))
            {
                //正しく入力されているかチェック
                try
                {
                    call_user_func(array($this, "clean_".$name));
                }
                catch (ValidationException $e)
                {
                    $this->errors[$name] = $e->getMessage();
                }
            }
        }

	//確認ボタンをクリックした場合のみ
	if($this->data["act"] == "conf"){

        //アップロードされたファイルに関する確認
	if(!isset($_FILES["paper"])){
		//不正アクセス
                $this->errors["paper"] = "入力してください";
	}else{

		if($_FILES["paper"]["error"] !== UPLOAD_ERR_OK)
		{

			//アップロードに失敗した場合
        		if($_FILES["paper"]["error"] == UPLOAD_ERR_INI_SIZE)
			{
				$this->errors["paper"] = "サイズが大きすぎます";
        		}
			else if($_FILES["paper"]["error"] == UPLOAD_ERR_NO_FILE)
			{
                		$this->errors["paper"] = "入力してください";
        		}else{
                		$this->errors["paper"] = "アップロードできません";
			}

		}else{
			//アップロードに成功した場合
			//拡張子の確認
			$exp_paper = explode(".",$_FILES["paper"]["name"]);

			if(count($exp_paper) > 1){
				//拡張子が存在する場合はpdfであるか確認
				if(strcasecmp($exp_paper[count($exp_paper)-1], "pdf") !== 0)
				{
					$this->errors["paper"] = "拡張子が正しくありません";
        			}
			}else{
				//Macなどで拡張子が存在しない場合は何もしない
			}

		}

        }

	if(empty($this->errors))
	{

		//古い一時ファイルの削除
		if(($handle = opendir(TMP_DIR)))
		{
			while(($file = readdir($handle)) !== false)
			{
				//ファイルであり、拡張子がpdfであり、十分古い場合は削除
				if(is_file(TMP_DIR.$file))
				{
					$exp_paper = explode(".",$file);

					if(count($exp_paper) > 1){
						if(strcasecmp($exp_paper[count($exp_paper)-1], "pdf") === 0)
						{
							if((time() - filemtime(TMP_DIR.$file)) > 600){
								unlink(TMP_DIR.$file);
							}
						}
        				}

				}
			}

			closedir($handle);
		}

		//一時ファイルのアップロード
        	$this->tmp_paper_name = "tmp_".h($this->data["first_name_ruby"])."_".h($this->data["last_name_ruby"])."_".date("Ymd_His").".pdf";

		if(!move_uploaded_file($_FILES["paper"]["tmp_name"],TMP_DIR.$this->tmp_paper_name))
		{
			$this->errors["paper"] = "アップロードできません";
		}

	}

	}

        //エラーメッセージが作成されていない時Trueを返す
        return empty($this->errors);
    }

    protected function clean_last_name_ruby()
    {
	if (!preg_match("/^[\x20-\x7e]+$/", $this->data["last_name_ruby"]))
	{
	    throw new ValidationException("指定文字で入力してください");
	}
    }

    protected function clean_first_name_ruby()
    {
	if (!preg_match("/^[\x20-\x7e]+$/", $this->data["first_name_ruby"]))
	{
	    throw new ValidationException("指定文字で入力してください");
	}
    }

    protected function clean_email()
    {
        if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $this->data["email"]))
        {
            throw new ValidationException("入力が正しくありません");
        }
    }

    protected function clean_email_retype()
    {
        if ($this->data["email_retype"] !== $this->data["email"])
        {
            throw new ValidationException("入力が一致しません");
        }
    }

    public function save()
    {
	$this->add_to_pdf();
        $this->add_to_csv();
    }

    private function add_to_pdf()
    {
	$this->tmp_paper_name = $this->data["tmp_paper_name"];
	$this->pdf_paper_name = h($this->data["first_name_ruby"])."_".h($this->data["last_name_ruby"])."_".date("Ymd_His").".pdf";

	rename(TMP_DIR.$this->tmp_paper_name, PDF_DIR.$this->pdf_paper_name);
    }

    // 入力された情報をCSVに追記する
    private function add_to_csv()
    {
        $exists = file_exists(CSV_PATH);
        $handle = @fopen(CSV_PATH, "a");
        @flock($handle,LOCK_EX);

        try
        {
            $lines = array();

            if (!$exists)
            {
                // ファイルが存在しない時は項目名も出力する
                $lines[] = array("名前", "Name", "所属", "メールアドレス", "参加区分", "発表区分", "ファイル名", "備考欄", "入力日時");
            }

            $lines[] = array(
		$this->data["last_name"]." ".$this->data["first_name"],
		$this->data["first_name_ruby"]." ".$this->data["last_name_ruby"],
		$this->data["affiliation"],
		$this->data["email"],
		($this->data["attend_type"] == "1") ? "一般" : "学生",
		($this->data["presentation_type"] !== "4") ? (($this->data["presentation_type"] !== "3") ? (($this->data["presentation_type"] !== "2") ? "特別" : "ショート") : "ロング") : "どちらも可",
		$this->pdf_paper_name,
		$this->data["notes"],
                date("Y/m/d H:i:s"),
            );

            foreach ($lines as $line)
            {
                $converted_line = array();
                foreach ($line as $item)
                {
                    // ExcelはUTF-8が読めないためSJISに変換して書き込む
                    $converted_line[] = mb_convert_encoding($item, "Shift_JIS", "UTF-8");
                }

                @fputcsv($handle, $converted_line);
            }

            @flock($handle,LOCK_UN);
            @fclose($handle);
        }
        catch (Exception $e)
        {
            @flock($handle,LOCK_UN);
            @fclose($handle);
        }
    }

}

// HTML文字のエスケープとマジッククォートの処理
function h($value)
{
    $value = htmlspecialchars($value, ENT_QUOTES, "UTF-8");

    if (get_magic_quotes_gpc())
    {
        $value = stripslashes($value);
    }

    return $value;
}

function send_email($form)
{
    //日本語メールを送る
    $to = h($form->data["email"]); //宛先
    $subject = "2017 SSOR Abstract"; //題名
    $from = "ssor.committee@gmail.com"; //差出人
    $fromname = "SSOR Committee"; //差し出し人名

    $_name = h($form->data["last_name"])." ".h($form->data["first_name"]);
    $_name_ruby = h($form->data["first_name_ruby"])." ".h($form->data["last_name_ruby"]);
    $_affiliation = h($form->data["affiliation"]);
    $_email = h($form->data["email"]);
    $_attend_type = (($form->data["attend_type"] == "1") ? "一般" : "学生");
    $_presentation_type = (($form->data["presentation_type"] !== "4") ? (($form->data["presentation_type"] !== "3") ? (($form->data["presentation_type"] !== "2") ? "特別講演" : "ショート発表") : "ロング発表") : "どちらも可");
    $_notes = h($form->data["notes"]);

    $body = <<< EOF
$_name 様

この度は2017年 中国・四国地区ＳＳＯＲへ
アブストラクトをご投稿いただきありがとうございました。

承りましたお申込み内容は下記のようになっております。

お名前：$_name
Name：$_name_ruby
ご所属：$_affiliation
メールアドレス：$_email
参加区分：$_attend_type
発表区分：$_presentation_type
アブストラクトファイル：添付ファイル参照
備考欄：
$_notes

内容に誤りがある場合や、変更をご希望される場合には、
下記問い合わせ先までご連絡くださいますようお願いいたします。

問い合わせ先：junji@sse.tottori-u.ac.jp

☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆
本メールにお心当たりのない場合は、お手数ですが

junji@sse.tottori-u.ac.jp

までご連絡いただくと共に、本メールの破棄をお願いいたします。
☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆☆
EOF;

    $mail = new JPHPMailer();

    $mail = new JPHPMailer();
    //$mail-> in_enc = "EUC-JP";
	//gmail use
	//https://www.google.com/settings/security/lesssecureapps で on
	//$mail->CharSet = 'EUC-JP';
	//$mail->Encoding = "7bit";
	$mail->IsSMTP();
    $mail->Host="ssl://smtp.gmail.com:465";
    $mail->SMTPAuth = TRUE;
    $mail->Username="ssor.committee@gmail.com";
    $mail->Password="ssor2017";

    $mail->addTo($to);
    //$mail->setFrom($from,$fromname);
    //$fromname=mb_encode_mimeheader($fromname, 'ISO-2022-JP');
    $mail->setFrom($mail->Username,$fromname);
    //$mail->setSubject($subject);

    //$subject=mb_convert_encoding($subject, 'EUC-JP', mb_detect_encoding($subject));
    //$subject=mb_encode_mimeheader($subject, 'utf-8');
    $mail->setSubject($subject);
    //$mail->Subject=$subject;
    $mail->addCc('ssor.committee@gmail.com');

    $body=mb_convert_encoding($body, "ISO-2022-JP", "UTF-8");
    //$body=mb_convert_encoding($body, "JIS", "EUC-JP");
    $mail->setBody($body);
    $mail->AddAttachment(PDF_DIR.$form->pdf_paper_name);



    $mail->send();


	//管理者へ通知メール
	/*
		$to = "ssor.committee@gmail.com"; //宛先

    $mail = new JPHPMailer();
	//ml.sse use
	//$mail->CharSet = 'SHIF-JIS';
	//$mail->Encoding = "7bit";
	$mail->IsSMTP();
	$mail->Host="ssl://tumail-smtp.center.tottori-u.ac.jp:465";
    $mail->SMTPAuth = TRUE;
    $mail->Username="ssor2017@ml.sse.tottori-u.ac.jp";
    $mail->Password="2017ssor";

    $mail->addTo($to);
    //$mail->setFrom($from,$fromname);
    $mail->setFrom($mail->Username, $fromname);
    $mail->setSubject($subject);
    $mail->setBody($body);
    $mail->AddAttachment(PDF_DIR.$form->pdf_paper_name);

    $mail->send();
    */
}

// XHTML(実際に表示される)
/******************************************************************
                             入力画面
******************************************************************/
function scr_ent()
{
    global $form;
?>

<h3>アブストラクト投稿</h3>
<p>
各項目をご入力の上、確認ボタンをクリックしてください。<br>
なお、頂いた個人情報につきましては、ＳＳＯＲの運営以外の用途には使用いたしません。
</p>

<form method="post" action="" enctype="multipart/form-data">

<table id="form">
<tr>

<th>
<label for="id_last_name">お名前</label>
</th>

<td class="class_text">
<span>
<label for="id_last_name">姓</label>
<input type="text" class="styled" id="id_last_name" name="last_name" value="<?php echo h($form->data["last_name"]) ?>" />
</span>

<span>
<label for="id_first_name">名</label>
<input type="text" class="styled" id="id_first_name" name="first_name" value="<?php echo h($form->data["first_name"]) ?>" />
<?php
if ($form->error_message("last_name") !== "")
	{
		echo $form->error_message("last_name");
        }
else
	{
		echo $form->error_message("first_name");
	}
?>
</span>
</td>

</tr>
<tr>

<th>
<label for="id_first_name_ruby">Name<br>（半角英数字のみ）</label>
</th>

<td class="class_text">
<span>
<label for="id_first_name_ruby">Given</label>
<input type="text" class="styled" id="id_first_name_ruby" name="first_name_ruby" value="<?php echo h($form->data["first_name_ruby"]) ?>" />
</span>

<span>
<label for="id_last_name_ruby">Family</label>
<input type="text" class="styled" id="id_last_name_ruby" name="last_name_ruby" value="<?php echo h($form->data["last_name_ruby"]) ?>" />
<?php
if ($form->error_message("last_name_ruby") == "<em>指定文字で入力してください</em>")
	{
		echo $form->error_message("last_name_ruby");
        }
else if ($form->error_message("first_name_ruby") == "<em>指定文字で入力してください</em>")
	{
		echo $form->error_message("first_name_ruby");
	}
else if ($form->error_message("last_name_ruby") !== "")
	{
		echo $form->error_message("last_name_ruby");
        }
else
	{
		echo $form->error_message("first_name_ruby");
	}
?>
</span>
</td>

</tr>
<tr>

<th>
<label for="id_affiliation">ご所属</label>
</th>

<td class="class_text">
<div>
<input type="text" class="styled" id="id_affiliation" name="affiliation" value="<?php echo h($form->data["affiliation"]) ?>" />
<?php echo $form->error_message("affiliation") ?>
</div>
</td>

</tr>
<tr>

<th>
<label for="id_email">メールアドレス</label>
</th>

<td class="class_text">
<div>
<input type="text" class="styled" id="id_email" name="email" value="<?php echo h($form->data["email"]) ?>" />
<?php echo $form->error_message("email") ?>
</div>
</td>

</tr>
<tr>

<th>
<label for="id_email_retype">メールアドレス<br>（再入力）</label>
</th>

<td class="class_text">
<div>
<input type="text" class="styled" id="id_email_retype" name="email_retype" value="<?php echo h($form->data["email_retype"]) ?>" />
<?php echo $form->error_message("email_retype") ?>
</div>
</td>

</tr>
<tr>

<th>
参加区分
</th>

<td class="class_text">
<div>
<input type="radio" id="id_general" name="attend_type" value="1" <?php if($form->data["attend_type"] == "1"){echo "checked";} ?> />
<label for="id_general">一般</label>
&nbsp;
<input type="radio" id="id_student" name="attend_type" value="0" <?php if($form->data["attend_type"] == "0"){echo "checked";} ?> />
<label for="id_student">学生</label>
&nbsp;&nbsp;
<?php echo $form->error_message("attend_type") ?>
</div>
</td>

</tr>
<tr>

<th>
発表区分
</th>

<td class="class_text">
<div>
<input type="radio" id="id_presentation_organized" name="presentation_type" value="2" <?php if($form->data["presentation_type"] == "2"){echo "checked";} ?> />
<label for="id_presentation_organized">ショート発表</label>
&nbsp;&nbsp;&nbsp;
<input type="radio" id="id_presentation_general" name="presentation_type" value="3" <?php if($form->data["presentation_type"] == "3"){echo "checked";} ?> />
<label for="id_presentation_general">ロング発表</label>
<br>
<input type="radio" id="id_presentation_student" name="presentation_type" value="4" <?php if($form->data["presentation_type"] == "4"){echo "checked";} ?> />
<label for="id_presentation_student">ショート，ロングどちらも可</label>
&nbsp;&nbsp;
<input type="radio" id="id_presentation_special" name="presentation_type" value="1" <?php if($form->data["presentation_type"] == "1"){echo "checked";} ?> />
<label for="id_presentation_special">特別講演</label>
&nbsp;
<?php echo $form->error_message("presentation_type") ?>
</div>
</td>

</tr>
<tr>

<th>
<label for="id_paper">アブストラクト<br>ファイル<br>（2MB以下の<br>pdfのみ）</label>
</th>

<td class="class_text">
<div>
<input type="file" class="styled" id="id_paper" name="paper" />
<?php echo $form->error_message("paper") ?>
</div>
</td>

</tr>
<tr>

<th>
<label for="id_notes">備考欄</label>
</th>

<td class="class_text">
<div>
<textarea class="styled" id="id_notes" name="notes" cols=70 rows=4>
<?php echo h($form->data["notes"]) ?>
</textarea>
&nbsp;&nbsp;
<?php echo $form->error_message("notes") ?>
</div>
</td>

</tr>
</table>

<div>
<input type="hidden" name="act" value = "conf">
</div>

<p id="id_button">
<input type="submit" class="styled" id="id_conf" value="確認" />
</p>

</form>

<?php
}

/******************************************************************
                             確認画面
******************************************************************/
function scr_conf()
{
    global $form;
?>

<h3>確認</h3>
<p>
お申込み内容をご確認ください。<br>
変更される場合は変更ボタンを、このままご投稿される場合は投稿ボタンをクリックしてください。
</p>

<table id="form">
<tr>

<th>
お名前
</th>

<td class="class_text">
<?php echo h($form->data["last_name"]) ?>
&nbsp;
<?php echo h($form->data["first_name"]) ?>
</td>

</tr>
<tr>

<th>
Name
</th>

<td class="class_text">
<?php echo h($form->data["first_name_ruby"]) ?>
&nbsp;
<?php echo h($form->data["last_name_ruby"]) ?>
</td>

</tr>
<tr>

<th>
ご所属
</th>

<td class="class_text">
<?php echo h($form->data["affiliation"]) ?>
</td>

</tr>
<tr>

<th>
メールアドレス
</th>

<td class="class_text">
<?php echo h($form->data["email"]) ?>
</td>

</tr>
<tr>

<th>
参加区分
</th>

<td class="class_text">
<?php
	if ($form->data["attend_type"] == "1")
	{
		echo "一般";
	}else{
		echo "学生";
	}
?>
</td>

</tr>
<tr>

<th>
発表区分
</th>

<td class="class_text">
<?php
	if ($form->data["presentation_type"] == "1")
	{
		echo "特別講演";

	}else if ($form->data["presentation_type"] == "2")
	{
		echo "ショート発表";

	}else if ($form->data["presentation_type"] == "3")
	{
		echo "ロング発表";

	}else{
		echo "ショート，ロングどちらも可";
	}
?>
</td>

</tr>
<tr>

<th>
アブストラクトファイル
</th>

<td class="class_text">
<?php echo h($_FILES["paper"]["name"]) ?>
</td>

</tr>
<tr>

<th>
備考欄
</th>

<td class="class_text">
<?php echo nl2br(h($form->data["notes"])) ?>
</td>

</tr>
</table>

<table id="id_buttons">
<tr>
<td class="class_button">
<form method="post" action="" enctype="multipart/form-data">

<input type="hidden" name="last_name" value="<?php echo h($form->data["last_name"]) ?>" />
<input type="hidden" name="first_name" value="<?php echo h($form->data["first_name"]) ?>" />
<input type="hidden" name="last_name_ruby" value="<?php echo h($form->data["last_name_ruby"]) ?>" />
<input type="hidden" name="first_name_ruby" value="<?php echo h($form->data["first_name_ruby"]) ?>" />
<input type="hidden" name="affiliation" value="<?php echo h($form->data["affiliation"]) ?>" />
<input type="hidden" name="email" value="<?php echo h($form->data["email"]) ?>" />
<input type="hidden" name="email_retype" value="<?php echo h($form->data["email_retype"]) ?>" />
<input type="hidden" name="attend_type" value="<?php echo $form->data["attend_type"] ?>" />
<input type="hidden" name="presentation_type" value="<?php echo $form->data["presentation_type"] ?>" />
<input type="hidden" name="tmp_paper_name" value="<?php echo $form->tmp_paper_name ?>" />
<input type="hidden" name="notes" value="<?php echo $form->data["notes"] ?>" />

<input type="hidden" name="act" value = "sub">
<input type="submit" class="styled" id="id_sub" value="投稿" />
</form>
</td>

<td class="class_button">
<form method="post" action="" enctype="multipart/form-data">

<input type="hidden" name="last_name" value="<?php echo h($form->data["last_name"]) ?>" />
<input type="hidden" name="first_name" value="<?php echo h($form->data["first_name"]) ?>" />
<input type="hidden" name="last_name_ruby" value="<?php echo h($form->data["last_name_ruby"]) ?>" />
<input type="hidden" name="affiliation" value="<?php echo h($form->data["affiliation"]) ?>" />
<input type="hidden" name="first_name_ruby" value="<?php echo h($form->data["first_name_ruby"]) ?>" />
<input type="hidden" name="email" value="<?php echo h($form->data["email"]) ?>" />
<input type="hidden" name="email_retype" value="<?php echo h($form->data["email_retype"]) ?>" />
<input type="hidden" name="attend_type" value="<?php echo $form->data["attend_type"] ?>" />
<input type="hidden" name="presentation_type" value="<?php echo $form->data["presentation_type"] ?>" />
<input type="hidden" name="notes" value="<?php echo $form->data["notes"] ?>" />

<input type="hidden" name="act" value = "ent">
<input type="submit" class="styled" id="id_ent" value="変更" />

</form>
</td>
</tr>
</table>
（ボタンを押してから，処理に時間がかかる場合がございます（２分程度），しばらくお待ちください．）

<?php
}

/******************************************************************
                           送信完了画面
******************************************************************/
function scr_sub()
{
?>

<h3>アブストラクト投稿完了</h3>

<p>
2017年 中国・四国地区ＳＳＯＲにアブストラクトをご投稿いただきありがとうございました。<br>
ご入力いただいたメールアドレスに確認メールをお送りしております。<br>
<br>
確認メールが届いていない、お申込み内容の変更を行いたいなどの<br>
お問い合わせ・ご要望がございましたら、お手数ですが、<br>
下記問い合わせ先までご連絡をお願いいたします。<br>
<br>
問い合わせ先：junji@sse.tottori-u.ac.jp<br>
<br>
なお、迷惑メールフィルターの設定により、<br>
確認メールが迷惑メールに分類される場合もございます。<br>
お問い合せの前に、一度ご確認いただきますようお願いいたします。
</p>

<?php
}
?>
