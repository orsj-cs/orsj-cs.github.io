<?php

/******************************************************************
2017 中国・四国地区 SSOR 用に以下のフォームを改変
 待ち行列シンポジウム 参加・発表申込みフォーム by Hirai 2015/09/10
 
******************************************************************/

//EUC と UTF の変更
//	EUC--JP と UTF--8 を置換（-- は - )する．（７ヶ所）
//	protected function clean_last_name_ruby()
//	protected function clean_first_name_ruby() を 各コード用に変更

/*---------------------------------------------------------------*/
/* 以下のdefineで始まる行に設定を記述してください.               */
/*                                                               */
/* CSV_PATH: 名簿データを蓄積するCSVファイルのパス.              */
/*           親ディレクトリが存在すること.                       */
/* 例："home/tsugu/public_html/anniversary/csv/namelist.csv"     */
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
/*---------------------------------------------------------------*/

//項目を増やしたときには，hidden フィールドもふやしておくこと
//awrad_entry は，学生の補助希望に流用

// PHP(フォームの内容を処理する)
define("CSV_PATH", "./csv/regist_data2017.csv");

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

<title>2017年 中国・四国地区ＳＳＯＲ 参加・発表申込み</title>
<link rel="stylesheet" href="./css/style.css" type="text/css" />
</head>

<body>

<div id="container">

<h2 id="title">2017年 中国・四国地区ＳＳＯＲ 参加・発表申込み</h2>

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
<a href="http://www.orsj.or.jp/chu_shikoku/ssor2017/index.html">トップページへ戻る</a>
</p>

</div>

</body>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">google.load("jquery", "1.6.4");</script>
<script type="text/javascript" src="js/jquery.load-modules3.js"></script>
<script type="text/javascript">
$(function(){

    //$("#id_general").click(function(){
    //    $("#id_table_award_entry").hide();
    //});

    //$("#id_student").click(function(){
	//if($("#id_presenter").attr("checked")) {
    //        $("#id_table_award_entry").show();
	//}
    //});

	//SSOR
    //if(!$("#id_student").attr("checked")) {
    //        $("#id_table_award_entry").hide();
	//}
	
	
    if(!$("#id_presenter").attr("checked")) {
        $("#id_table_presentation_type").hide();
        //$("#id_table_award_entry").hide();

        $("#id_table_paper_name").hide();

        $("#id_table_1author_name").hide();
        $("#id_table_1author_affiliation").hide();
        $("#id_table_1author_presenter").hide();

        $("#id_table_2author_name").hide();
        $("#id_table_2author_affiliation").hide();
        $("#id_table_2author_presenter").hide();

        $("#id_table_3author_name").hide();
        $("#id_table_3author_affiliation").hide();
        $("#id_table_3author_presenter").hide();

        $("#id_table_4author_name").hide();
        $("#id_table_4author_affiliation").hide();
        $("#id_table_4author_presenter").hide();

        $("#id_table_5author_name").hide();
        $("#id_table_5author_affiliation").hide();
        $("#id_table_5author_presenter").hide();
        $("#id_table_5author_note").hide();
    }

//    if($("#id_presenter").attr("checked")) {
//	if($("#id_general").attr("checked")) {
//            $("#id_table_award_entry").hide();
//	}
//    }

//	if(!$("#id_general").attr("checked")) {
//           $("#id_table_award_entry").show();
//	}

    $("#id_presenter").click(function(){
        $("#id_table_presentation_type").show();
//	if(!$("#id_general").attr("checked")) {
//           $("#id_table_award_entry").show();
//	}

        $("#id_table_paper_name").show();

        $("#id_table_1author_name").show();
        $("#id_table_1author_affiliation").show();
        $("#id_table_1author_presenter").show();

        $("#id_table_2author_name").show();
        $("#id_table_2author_affiliation").show();
        $("#id_table_2author_presenter").show();

        $("#id_table_3author_name").show();
        $("#id_table_3author_affiliation").show();
        $("#id_table_3author_presenter").show();

        $("#id_table_4author_name").show();
        $("#id_table_4author_affiliation").show();
        $("#id_table_4author_presenter").show();

        $("#id_table_5author_name").show();
        $("#id_table_5author_affiliation").show();
        $("#id_table_5author_presenter").show();
        $("#id_table_5author_note").show();
    });

    $("#id_audience").click(function(){
        $("#id_table_presentation_type").hide();
        //$("#id_table_award_entry").hide();

        $("#id_table_paper_name").hide();

        $("#id_table_1author_name").hide();
        $("#id_table_1author_affiliation").hide();
        $("#id_table_1author_presenter").hide();

        $("#id_table_2author_name").hide();
        $("#id_table_2author_affiliation").hide();
        $("#id_table_2author_presenter").hide();

        $("#id_table_3author_name").hide();
        $("#id_table_3author_affiliation").hide();
        $("#id_table_3author_presenter").hide();

        $("#id_table_4author_name").hide();
        $("#id_table_4author_affiliation").hide();
        $("#id_table_4author_presenter").hide();

        $("#id_table_5author_name").hide();
        $("#id_table_5author_affiliation").hide();
        $("#id_table_5author_presenter").hide();
        $("#id_table_5author_note").hide();
    });

    if($("#id_confirm_presentation").val() == 0) {
        $("#id_confirm_table_presentation_type").hide();
        //$("#id_confirm_table_award_entry").hide();

        $("#id_confirm_table_paper_name").hide();

        $("#id_confirm_table_1author_name").hide();
        $("#id_confirm_table_1author_affiliation").hide();
        $("#id_confirm_table_1author_presenter").hide();

        $("#id_confirm_table_2author_name").hide();
        $("#id_confirm_table_2author_affiliation").hide();
        $("#id_confirm_table_2author_presenter").hide();

        $("#id_confirm_table_3author_name").hide();
        $("#id_confirm_table_3author_affiliation").hide();
        $("#id_confirm_table_3author_presenter").hide();

        $("#id_confirm_table_4author_name").hide();
        $("#id_confirm_table_4author_affiliation").hide();
        $("#id_confirm_table_4author_presenter").hide();

        $("#id_confirm_table_5author_name").hide();
        $("#id_confirm_table_5author_affiliation").hide();
        $("#id_confirm_table_5author_presenter").hide();
    }

    //if($("#id_confirm_presentation").val() == 1) {
    //	if($("#id_confirm_attend_type").val() == 1) {
    //        $("#id_confirm_table_award_entry").hide();
    //    }
    //}

    if($("#id_confirm_2author_last_name").val() == "") {
        $("#id_confirm_table_2author_name").hide();
        $("#id_confirm_table_2author_affiliation").hide();
        $("#id_confirm_table_2author_presenter").hide();
    }

    if($("#id_confirm_3author_last_name").val() == "") {
        $("#id_confirm_table_3author_name").hide();
        $("#id_confirm_table_3author_affiliation").hide();
        $("#id_confirm_table_3author_presenter").hide();
    }

    if($("#id_confirm_4author_last_name").val() == "") {
        $("#id_confirm_table_4author_name").hide();
        $("#id_confirm_table_4author_affiliation").hide();
        $("#id_confirm_table_4author_presenter").hide();
    }

    if($("#id_confirm_5author_last_name").val() == "") {
        $("#id_confirm_table_5author_name").hide();
        $("#id_confirm_table_5author_affiliation").hide();
        $("#id_confirm_table_5author_presenter").hide();
    }

});
</script>

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
    public $inputs; 

    function __construct($data = null)
    {
        parent::__construct($data);

	$this->inputs = array(
		"2author" => ((($this->data["2author_last_name"] !== "") || ($this->data["2author_first_name"] !== "") || ($this->data["2author_affiliation"] !== "") || ($this->data["presenter"] == "2")) ? true : false),
		"3author" => ((($this->data["3author_last_name"] !== "") || ($this->data["3author_first_name"] !== "") || ($this->data["3author_affiliation"] !== "") || ($this->data["presenter"] == "3")) ? true : false),
		"4author" => ((($this->data["4author_last_name"] !== "") || ($this->data["4author_first_name"] !== "") || ($this->data["4author_affiliation"] !== "") || ($this->data["presenter"] == "4")) ? true : false),
		"5author" => ((($this->data["5author_last_name"] !== "") || ($this->data["5author_first_name"] !== "") || ($this->data["5author_affiliation"] !== "") || ($this->data["presenter"] == "5")) ? true : false),
	);

        $this->fields = array(
		"last_name" => array("required" => true), //入力必須
		"first_name" => array("required" => true),
		"last_name_ruby" => array("required" => true),
		"first_name_ruby" => array("required" => true),
		"gender" => array("required" => true),
		"affiliation" => array("required" => true),
		"email" => array("required" => true),
		"email_retype" => array("required" => true),
		"attend_type" => array("required" => true),
		"teacher" => array("required" => false),
		"receipt" => array("required" => true),
		"presentation" => array("required" => true),

		"presentation_type" => array("required" => ($this->data["presentation"] == "1") ? true : false),
//		"award_entry" => array("required" => (($this->data["presentation"] == "1") && ($this->data["attend_type"] == "0")) ? true : false),
//		"award_entry" => array("required" => ($this->data["attend_type"] == "0") ? true : false),
		"award_entry" => array("required" => true),

		"paper_name" => array("required" => ($this->data["presentation"] == "1") ? true : false),
		"lang_type" => array("required" => ($this->data["presentation"] == "1") ? true : false),

		"1author_last_name" => array("required" => ($this->data["presentation"] == "1") ? true : false),
		"1author_first_name" => array("required" => ($this->data["presentation"] == "1") ? true : false),
		"1author_affiliation" => array("required" => ($this->data["presentation"] == "1") ? true : false),

		"2author_last_name" => array("required" => (($this->data["presentation"] == "1") && (($this->inputs["2author"]) || ($this->inputs["3author"]) || ($this->inputs["4author"]) || ($this->inputs["5author"]))) ? true : false),
		"2author_first_name" => array("required" => (($this->data["presentation"] == "1") && (($this->inputs["2author"]) || ($this->inputs["3author"]) || ($this->inputs["4author"]) || ($this->inputs["5author"]))) ? true : false),
		"2author_affiliation" => array("required" => (($this->data["presentation"] == "1") && (($this->inputs["2author"]) || ($this->inputs["3author"]) || ($this->inputs["4author"]) || ($this->inputs["5author"]))) ? true : false),

		"3author_last_name" => array("required" => (($this->data["presentation"] == "1") && (($this->inputs["3author"]) || ($this->inputs["4author"]) || ($this->inputs["5author"]))) ? true : false),
		"3author_first_name" => array("required" => (($this->data["presentation"] == "1") && (($this->inputs["3author"]) || ($this->inputs["4author"]) || ($this->inputs["5author"]))) ? true : false),
		"3author_affiliation" => array("required" => (($this->data["presentation"] == "1") && (($this->inputs["3author"]) || ($this->inputs["4author"]) || ($this->inputs["5author"]))) ? true : false),

		"4author_last_name" => array("required" => (($this->data["presentation"] == "1") && (($this->inputs["4author"]) || ($this->inputs["5author"]))) ? true : false),
		"4author_first_name" => array("required" => (($this->data["presentation"] == "1") && (($this->inputs["4author"]) || ($this->inputs["5author"]))) ? true : false),
		"4author_affiliation" => array("required" => (($this->data["presentation"] == "1") && (($this->inputs["4author"]) || ($this->inputs["5author"]))) ? true : false),

		"5author_last_name" => array("required" => (($this->data["presentation"] == "1") && ($this->inputs["5author"])) ? true : false),
		"5author_first_name" => array("required" => (($this->data["presentation"] == "1") && ($this->inputs["5author"])) ? true : false),
		"5author_affiliation" => array("required" => (($this->data["presentation"] == "1") && ($this->inputs["5author"])) ? true : false),

		"presenter" => array("required" => ($this->data["presentation"] == "1") ? true : false),
		"notes" => array("required" => false),
        );
    }

    //全てのフォームが正しく入力されている時Trueを返す
    public function is_valid()
    {
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
        //エラーメッセージが作成されていない時Trueを返す
        return empty($this->errors);
    }

    protected function clean_last_name_ruby()
    {
	if (!preg_match("/^(\xe3(\x82[\xa1-\xbf]|\x83[\x80-\xb6|\xbc]))+$/", $this->data["last_name_ruby"]))	//UTF--8
//	if(!preg_match("/^(\xa5[\xa1-\xf6]|\xa1[\xb3\xb4\xbc])+$/", $this->data["last_name_ruby"]))	//EUC--JP
	{
 	    throw new ValidationException("全角カタカナで入力してください");
	}
    }

    protected function clean_first_name_ruby()
    {
	if (!preg_match("/^(\xe3(\x82[\xa1-\xbf]|\x83[\x80-\xb6|\xbc]))+$/", $this->data["first_name_ruby"]))	//UTF--8
//	if (!preg_match("/^(\xa5[\xa1-\xf6]|\xa1[\xb3\xb4\xbc])+$/", $this->data["first_name_ruby"]))	//EUC--JP
	{
	    $this->errors["last_name_ruby"] = "全角カタカナで入力してください";
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
        $this->add_to_csv();
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
                $lines[] = array("名前", "フリガナ", "性別", "所属", "メールアドレス", "参加区分", "指導教員", "補助希望", "領収書", "発表", "発表区分", "題目", "言語", 
                	"一著名前", "一著所属", "一著発表者", "二著名前", "二著所属", "二著発表者", "三著名前", "三著所属", "三著発表者", 
                	"四著名前", "四著所属", "四著発表者", "五著名前", "五著所属", "五著発表者", 
                	"備考欄", "入力日時");
            }

            $lines[] = array(
		$this->data["last_name"]." ".$this->data["first_name"],
		$this->data["last_name_ruby"]." ".$this->data["first_name_ruby"],
		($this->data["gender"] == "1") ? "男" : "女",
		$this->data["affiliation"],
		$this->data["email"],

		($this->data["attend_type"] == "1") ? "一般" : "学生か 30 歳未満",
		($this->data["attend_type"] == "0") ? $this->data["teacher"] : "",
		($this->data["award_entry"] == "1") ? "する" : "しない",
		($this->data["receipt"] == "1") ? "要" : "不要",

		($this->data["presentation"] == "1") ? "有" : "無",
		($this->data["presentation"] == "1") ? (($this->data["presentation_type"] !== "4") ? (($this->data["presentation_type"] !== "3") ? (($this->data["presentation_type"] !== "2") ? "特別" : "ショート") : "ロング") : "両方可") : "",
		//($this->data["attend_type"] == "0") ? (($this->data["award_entry"] == "1") ? "する" : "しない") : "",

		($this->data["presentation"] == "1") ? $this->data["paper_name"] : "",
		($this->data["presentation"] == "1") ? (($this->data["lang_type"] == "1") ? "日本語" : "英語") : "",
		//($this->data["lang_type"] == "1") ? "出席" : "欠席",

		($this->data["presentation"] == "1") ? $this->data["1author_last_name"]." ".$this->data["1author_first_name"] : "",
		($this->data["presentation"] == "1") ? $this->data["1author_affiliation"] : "",
		($this->data["presentation"] == "1") ? (($this->data["presenter"] == "1") ? "○" : "×") : "",

		(($this->data["presentation"] == "1") && ($this->inputs["2author"])) ? $this->data["2author_last_name"]." ".$this->data["2author_first_name"] : "",
		(($this->data["presentation"] == "1") && ($this->inputs["2author"])) ? $this->data["2author_affiliation"] : "",
		(($this->data["presentation"] == "1") && ($this->inputs["2author"])) ? (($this->data["presenter"] == "2") ? "○" : "×") : "",

		(($this->data["presentation"] == "1") && ($this->inputs["3author"])) ? $this->data["3author_last_name"]." ".$this->data["3author_first_name"] : "",
		(($this->data["presentation"] == "1") && ($this->inputs["3author"])) ? $this->data["3author_affiliation"] : "",
		(($this->data["presentation"] == "1") && ($this->inputs["3author"])) ? (($this->data["presenter"] == "3") ? "○" : "×") : "",

		(($this->data["presentation"] == "1") && ($this->inputs["4author"])) ? $this->data["4author_last_name"]." ".$this->data["4author_first_name"] : "",
		(($this->data["presentation"] == "1") && ($this->inputs["4author"])) ? $this->data["4author_affiliation"] : "",
		(($this->data["presentation"] == "1") && ($this->inputs["4author"])) ? (($this->data["presenter"] == "4") ? "○" : "×") : "",

		(($this->data["presentation"] == "1") && ($this->inputs["5author"])) ? $this->data["5author_last_name"]." ".$this->data["5author_first_name"] : "",
		(($this->data["presentation"] == "1") && ($this->inputs["5author"])) ? $this->data["5author_affiliation"] : "",
		(($this->data["presentation"] == "1") && ($this->inputs["5author"])) ? (($this->data["presenter"] == "5") ? "○" : "×") : "",

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
    $subject = "SSOR Registration"; //題名
    $from = "junji@sse.tottori-u.ac.jp"; //差出人
    $fromname = "SSOR Committee"; //差し出し人名

    $_name = h($form->data["last_name"])." ".h($form->data["first_name"]);
    $_name_ruby = h($form->data["last_name_ruby"])." ".h($form->data["first_name_ruby"]);
    $_gender = (($form->data["gender"] == "1") ? "男" : "女");
    $_affiliation = h($form->data["affiliation"]);
    $_email = h($form->data["email"]);
    $_attend_type = (($form->data["attend_type"] == "1") ? "一般" : "学生か 30 歳未満");
    $_receipt = (($form->data["receipt"] == "1") ? "要" : "不要");


    $_presentation = (($form->data["presentation"] == "1") ? "有" : "無");
    $_teacher=(($form->data["attend_type"] == "0") ? h($form->data["teacher"]) : "");
    $_presentation_type = (($form->data["presentation_type"] !== "4") ? (($form->data["presentation_type"] !== "3") ? (($form->data["presentation_type"] !== "2") ? "特別講演" : "ショート発表") : "ロング発表") : "ショート，ロングどちらでも可");
    $_award_entry = (($form->data["award_entry"] == "1") ? "希望する" : "希望しない");

    $_paper_name = h($form->data["paper_name"]);
    $_lang_type = (($form->data["lang_type"] == "1") ? "日本語" : "英語");

    $_1author_name = h($form->data["1author_last_name"])." ".h($form->data["1author_first_name"]);
    $_1author_affiliation = h($form->data["1author_affiliation"]);
    $_1author_presenter = (($form->data["presenter"] == "1") ? "○" : "×");

    $_2author_name = h($form->data["2author_last_name"])." ".h($form->data["2author_first_name"]);
    $_2author_affiliation = h($form->data["2author_affiliation"]);
    $_2author_presenter = (($form->data["presenter"] == "2") ? "○" : "×");

    $_3author_name = h($form->data["3author_last_name"])." ".h($form->data["3author_first_name"]);
    $_3author_affiliation = h($form->data["3author_affiliation"]);
    $_3author_presenter = (($form->data["presenter"] == "3") ? "○" : "×");

    $_4author_name = h($form->data["4author_last_name"])." ".h($form->data["4author_first_name"]);
    $_4author_affiliation = h($form->data["4author_affiliation"]);
    $_4author_presenter = (($form->data["presenter"] == "4") ? "○" : "×");

    $_5author_name = h($form->data["5author_last_name"])." ".h($form->data["5author_first_name"]);
    $_5author_affiliation = h($form->data["5author_affiliation"]);
    $_5author_presenter = (($form->data["presenter"] == "5") ? "○" : "×");

    $_notes = h($form->data["notes"]);
	
	$_attendstr=$_attend_type;
	if ($form->data["attend_type"] == "0"){
		if (strlen($_teacher)>0){
			$_attendstr.=", 指導教員：".$_teacher;
		}
	//	$_attendstr.="ＯＲ学会補助：".$_award_entry;
	}
	$_attendstr.="\n"."ＯＲ学会補助：".$_award_entry;

    $body = <<< EOF
$_name 様

この度は2017年 中国・四国地区ＳＳＯＲへの参加・発表を
お申込みいただきありがとうございました。

承りましたお申込み内容は下記のようになっております。

お名前：$_name
フリガナ：$_name_ruby
性別：$_gender
ご所属：$_affiliation
メールアドレス：$_email
参加区分：$_attendstr
領収書：$_receipt
発表：$_presentation

EOF
;

    if ($form->data["presentation"] == "1")
        {
	    $tmp = <<< EOF
発表区分：$_presentation_type

EOF
;
	    $body .= $tmp;

/*
	    if ($form->data["attend_type"] == "0")
                {
	            $tmp = <<< EOF
ＯＲ学会の補助：$_award_entry

EOF;
		    $body .= $tmp;
                }
*/

	    $tmp = <<< EOF
題目：{$_paper_name}，発表言語：{$_lang_type}
----------------------------------------
第一著者
お名前：$_1author_name
ご所属：$_1author_affiliation
発表者：$_1author_presenter
----------------------------------------

EOF
;
	    $body .= $tmp;

	    if ($form->inputs["2author"])
		{
		    $tmp = <<< EOF
第二著者
お名前：$_2author_name
ご所属：$_2author_affiliation
発表者：$_2author_presenter
----------------------------------------

EOF
;
		    $body .= $tmp;
		}

	    if ($form->inputs["3author"])
		{
		    $tmp = <<< EOF
第三著者
お名前：$_3author_name
ご所属：$_3author_affiliation
発表者：$_3author_presenter
----------------------------------------

EOF
;
		    $body .= $tmp;
		}

	    if ($form->inputs["4author"])
		{
		    $tmp = <<< EOF
第四著者
お名前：$_4author_name
ご所属：$_4author_affiliation
発表者：$_4author_presenter
----------------------------------------

EOF
;
		    $body .= $tmp;
		}

if ($form->inputs["5author"])
		{
		    $tmp = <<< EOF
第五著者
お名前：$_5author_name
ご所属：$_5author_affiliation
発表者：$_5author_presenter
----------------------------------------

EOF
;
		    $body .= $tmp;
		}
	}

    $tmp = <<< EOF
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
EOF
;
    $body .= $tmp;

    $mail = new JPHPMailer();
    $mail->IsSMTP();
    $mail->Host="ssl://smtp.gmail.com:465";
    $mail->SMTPAuth = TRUE;
    $mail->Username="ssor.committee@gmail.com";
    $mail->Password="ssor2017";

    $mail->addTo($to);
    $mail->setFrom($mail->Username,$fromname);
    $mail->setSubject($subject);
    
    $body=mb_convert_encoding($body, "ISO-2022-JP", "UTF-8");
    $mail->setBody($body);
    $mail->send();

	/*
    $to = "junji@sse.tottori-u.ac.jp"; //宛先 

    $mail = new JPHPMailer();
    $mail->addTo($to);
    $mail->setFrom($from,$fromname);
    $mail->setSubject($subject);
    $mail->setBody($body);

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

<h3>参加・発表申込み</h3>
<p>
各項目をご入力の上、確認ボタンをクリックしてください。<br>
なお、頂いた個人情報につきましては、本シンポジウム運営以外の用途には使用いたしません。
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
<label for="id_last_name_ruby">フリガナ<br>（全角カタカナのみ）</label>
</th>

<td class="class_text">
<span>
<label for="id_last_name_ruby">セイ</label>
<input type="text" class="styled" id="id_last_name_ruby" name="last_name_ruby" value="<?php echo h($form->data["last_name_ruby"]) ?>" />
</span>

<span>			
<label for="id_first_name_ruby">メイ</label>
<input type="text" class="styled" id="id_first_name_ruby" name="first_name_ruby" value="<?php echo h($form->data["first_name_ruby"]) ?>" />
<?php
if ($form->error_message("last_name_ruby") !== "")
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

<!-- <th rowspan="2"> -->
<th>
性別
</th>

<td class="class_text">
<div>
<input type="radio" id="id_permit" name="gender" value="1" <?php if($form->data["gender"] == "1"){echo "checked";} ?> />
<label for="id_permit">男</label>
&nbsp;
<input type="radio" id="id_forbid" name="gender" value="0" <?php if($form->data["gender"] == "0"){echo "checked";} ?> />
<label for="id_forbid">女</label>
&nbsp;&nbsp;
<?php echo $form->error_message("gender") ?>
</div>
</td>

</tr>

<!--
<tr>

<td class="class_text">
＊シンポジウム当日に紙媒体にて配布されます
</td>

</tr>
-->


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
<label for="id_student">学生か 30 歳未満（下に指導教員か年齢を記入してください）</label>
<?php echo $form->error_message("attend_type") ?>
<br>
指導教員（学生のみ）または 9/7 時点の年齢（30 歳未満の一般）<input type="text" class="styled" id="id_teacher" name="teacher" value="<?php echo h($form->data["teacher"]) ?>" />
<?php echo $form->error_message("teacher") ?>
</div>
</td>

</tr>

<tr id="id_table_award_entry">

<th>
ＯＲ学会からの<br>
補助希望<br>
（学生か <br>
9/7 で 30 歳未満）
</th>

<td class="class_text">
<div>
<input type="radio" id="id_award_entry_yes" name="award_entry" value="1" <?php if($form->data["award_entry"] == "1"){echo "checked";} ?> />
<label for="id_award_entry_yes">希望する</label>
&nbsp;
<input type="radio" id="id_award_entry_no" name="award_entry" value="0" <?php if($form->data["award_entry"] == "0"){echo "checked";} ?> />
<label for="id_award_entry_no">希望しない</label>
&nbsp;&nbsp;
<?php echo $form->error_message("award_entry") ?>
</div>
</td>

</tr>

<tr>

<!-- <th rowspan="2"> -->
<th>
領収書
</th>

<td class="class_text">
<div>
<input checked type="radio" id="id_need" name="receipt" value="1" <?php if($form->data["receipt"] == "1"){echo "checked";} ?> />
<label for="id_permit">要</label>
&nbsp;
<input type="radio" id="id_noneed" name="receipt" value="0" <?php if($form->data["receipt"] == "0"){echo "checked";} ?> />
<label for="id_forbid">不要</label>
&nbsp;&nbsp;
<?php echo $form->error_message("gender") ?>
</div>
</td>

</tr>



<tr>

<th>
発表
</th>

<td class="class_text">
<div>
<input type="radio" id="id_presenter" name="presentation" value="1" <?php if($form->data["presentation"] == "1"){echo "checked";} ?> />
<label for="id_presenter">有</label>
&nbsp;
<input type="radio" id="id_audience" name="presentation" value="0" <?php if($form->data["presentation"] == "0"){echo "checked";} ?> />
<label for="id_audience">無</label>
&nbsp;&nbsp;（有を選択すると追加の項目が表示されます）&nbsp;&nbsp;
<?php echo $form->error_message("presentation") ?>
</div>
</td>

</tr>
<tr id="id_table_presentation_type">

<th>
発表区分
</th>

<td class="class_text">
<div>
<input type="radio" id="id_presentation_organized" name="presentation_type" value="2" <?php if($form->data["presentation_type"] == "2"){echo "checked";} ?> />
<label for="id_presentation_organized">ショート発表</label>
&nbsp;
<input type="radio" id="id_presentation_general" name="presentation_type" value="3" <?php if($form->data["presentation_type"] == "3"){echo "checked";} ?> />
<label for="id_presentation_general">ロング発表</label>
<br>
<input type="radio" id="id_presentation_student" name="presentation_type" value="4" <?php if($form->data["presentation_type"] == "4"){echo "checked";} ?> />
<label for="id_presentation_student">ショート，ロングどちらでも可</label>
&nbsp;
<input type="radio" id="id_presentation_special" name="presentation_type" value="1" <?php if($form->data["presentation_type"] == "1"){echo "checked";} ?> />
<label for="id_presentation_special">特別講演</label>
&nbsp;
<?php echo $form->error_message("presentation_type") ?>
</div>
</td>

</tr>


<tr id="id_table_paper_name">

<th>
<label for="id_paper_name">題目<br>（仮題で結構です）</label>
</th>

<td class="class_text">
<div>
<input type="text" class="styled" id="id_paper_name" name="paper_name" value="<?php echo h($form->data["paper_name"]) ?>" />
<?php echo $form->error_message("paper_name") ?>
</div>
<div>発表言語
<input checked type="radio" id="id_attend" name="lang_type" value="1" <?php if($form->data["lang_type"] == "1"){echo "checked";} ?> />
<label for="id_attend">日本語</label>
&nbsp;
<input type="radio" id="id_absent" name="lang_type" value="0" <?php if($form->data["lang_type"] == "0"){echo "checked";} ?> />
<label for="id_absent">英語</label>
&nbsp;&nbsp;
<?php echo $form->error_message("lang_type") ?>
</div>
</td>

</tr>

<tr id="id_table_1author_name">
<th rowspan="3">
<label for="id_1author_last_name">第一著者</label>
</th>

<td class="class_text">
<span>
<label for="id_1author_last_name">姓</label>
<input type="text" class="styled" id="id_1author_last_name" name="1author_last_name" value="<?php echo h($form->data["1author_last_name"]) ?>" />
</span>

<span>
<label for="id_1author_first_name">名</label>
<input type="text" class="styled" id="id_1author_first_name" name="1author_first_name" value="<?php echo h($form->data["1author_first_name"]) ?>" />
<?php
if ($form->error_message("1author_last_name") !== "")
	{
		echo $form->error_message("1author_last_name");
        }
else
	{
		echo $form->error_message("1author_first_name");
	}
?>
</span>
</td>
</tr>
<tr id="id_table_1author_affiliation">
<td class="class_text">
<div>
<span>
<label for="id_1author_affiliation">ご所属</label>
<input type="text" class="styled" id="id_1author_affiliation" name="1author_affiliation" value="<?php echo h($form->data["1author_affiliation"]) ?>" />
<?php echo $form->error_message("1author_affiliation") ?>
</span>
</div>
</td>
</tr>
<tr id="id_table_1author_presenter">
<td class="class_text">
<div>
<input type="radio" id="id_1author_presenter" name="presenter" value="1" <?php if($form->data["presenter"] == "1"){echo "checked";} ?> />
<label for="id_1author_presenter">発表者</label>
&nbsp;&nbsp;
<?php echo $form->error_message("presenter") ?>
</div>
</td>
</tr>
<tr id="id_table_2author_name">
<th rowspan="3">
<label for="id_2author_last_name">第二著者</label>
</th>

<td class="class_text">
<span>
<label for="id_2author_last_name">姓</label>
<input type="text" class="styled" id="id_2author_last_name" name="2author_last_name" value="<?php echo h($form->data["2author_last_name"]) ?>" />
</span>

<span>
<label for="id_2author_first_name">名</label>
<input type="text" class="styled" id="id_2author_first_name" name="2author_first_name" value="<?php echo h($form->data["2author_first_name"]) ?>" />
<?php
if ($form->error_message("2author_last_name") !== "")
	{
		echo $form->error_message("2author_last_name");
        }
else
	{
		echo $form->error_message("2author_first_name");
	}
?>
</span>
</td>
</tr>
<tr id="id_table_2author_affiliation">
<td class="class_text">
<div>
<span>
<label for="id_2author_affiliation">ご所属</label>
<input type="text" class="styled" id="id_2author_affiliation" name="2author_affiliation" value="<?php echo h($form->data["2author_affiliation"]) ?>" />
<?php echo $form->error_message("2author_affiliation") ?>
</span>
</div>
</td>
</tr>
<tr id="id_table_2author_presenter">
<td class="class_text">
<div>
<input type="radio" id="id_2author_presenter" name="presenter" value="2" <?php if($form->data["presenter"] == "2"){echo "checked";} ?> />
<label for="id_2author_presenter">発表者</label>
&nbsp;&nbsp;
<?php
if ($form->inputs["2author"])
	{
		echo $form->error_message("presenter");
        }
?>
</div>
</td>
</tr>
<tr id="id_table_3author_name">
<th rowspan="3">
<label for="id_3author_last_name">第三著者</label>
</th>

<td class="class_text">
<span>
<label for="id_3author_last_name">姓</label>
<input type="text" class="styled" id="id_3author_last_name" name="3author_last_name" value="<?php echo h($form->data["3author_last_name"]) ?>" />
</span>

<span>
<label for="id_3author_first_name">名</label>
<input type="text" class="styled" id="id_3author_first_name" name="3author_first_name" value="<?php echo h($form->data["3author_first_name"]) ?>" />
<?php
if ($form->error_message("3author_last_name") !== "")
	{
		echo $form->error_message("3author_last_name");
        }
else
	{
		echo $form->error_message("3author_first_name");
	}
?>
</span>
</td>
</tr>
<tr id="id_table_3author_affiliation">
<td class="class_text">
<div>
<span>
<label for="id_3author_affiliation">ご所属</label>
<input type="text" class="styled" id="id_3author_affiliation" name="3author_affiliation" value="<?php echo h($form->data["3author_affiliation"]) ?>" />
<?php echo $form->error_message("3author_affiliation") ?>
</span>
</div>
</td>
</tr>
<tr id="id_table_3author_presenter">
<td class="class_text">
<div>
<input type="radio" id="id_3author_presenter" name="presenter" value="3" <?php if($form->data["presenter"] == "3"){echo "checked";} ?> />
<label for="id_3author_presenter">発表者</label>
&nbsp;&nbsp;
<?php
if ($form->inputs["3author"])
	{
		echo $form->error_message("presenter");
        }
?>
</div>
</td>
</tr>
<tr id="id_table_4author_name">
<th rowspan="3">
<label for="id_4author_last_name">第四著者</label>
</th>

<td class="class_text">
<span>
<label for="id_4author_last_name">姓</label>
<input type="text" class="styled" id="id_4author_last_name" name="4author_last_name" value="<?php echo h($form->data["4author_last_name"]) ?>" />
</span>

<span>
<label for="id_4author_first_name">名</label>
<input type="text" class="styled" id="id_4author_first_name" name="4author_first_name" value="<?php echo h($form->data["4author_first_name"]) ?>" />
<?php
if ($form->error_message("4author_last_name") !== "")
	{
		echo $form->error_message("4author_last_name");
    }
else
	{
		echo $form->error_message("4author_first_name");
	}
?>
</span>
</td>
</tr>
<tr id="id_table_4author_affiliation">
<td class="class_text">
<div>
<span>
<label for="id_4author_affiliation">ご所属</label>
<input type="text" class="styled" id="id_4author_affiliation" name="4author_affiliation" value="<?php echo h($form->data["4author_affiliation"]) ?>" />
<?php echo $form->error_message("4author_affiliation") ?>
</span>
</div>
</td>
</tr>
<tr id="id_table_4author_presenter">
<td class="class_text">
<div>
<input type="radio" id="id_4author_presenter" name="presenter" value="4" <?php if($form->data["presenter"] == "4"){echo "checked";} ?> />
<label for="id_4author_presenter">発表者</label>
&nbsp;&nbsp;
<?php
if ($form->inputs["4author"])
	{
		echo $form->error_message("presenter");
        }
?>
</div>
</td>
</tr>
<tr id="id_table_5author_name">
<th rowspan="4">
<label for="id_5author_last_name">第五著者</label>
</th>

<td class="class_text">
<span>
<label for="id_5author_last_name">姓</label>
<input type="text" class="styled" id="id_5author_last_name" name="5author_last_name" value="<?php echo h($form->data["5author_last_name"]) ?>" />
</span>

<span>
<label for="id_5author_first_name">名</label>
<input type="text" class="styled" id="id_5author_first_name" name="5author_first_name" value="<?php echo h($form->data["5author_first_name"]) ?>" />
<?php
if ($form->error_message("5author_last_name") !== "")
	{
		echo $form->error_message("5author_last_name");
        }
else
	{
		echo $form->error_message("5author_first_name");
	}
?>
</span>
</td>
</tr>
<tr id="id_table_5author_affiliation">
<td class="class_text">
<div>
<span>
<label for="id_5author_affiliation">ご所属</label>
<input type="text" class="styled" id="id_5author_affiliation" name="5author_affiliation" value="<?php echo h($form->data["5author_affiliation"]) ?>" />
<?php echo $form->error_message("5author_affiliation") ?>
</span>
</div>
</td>
</tr>
<tr id="id_table_5author_presenter">
<td class="class_text">
<div>
<input type="radio" id="id_5author_presenter" name="presenter" value="5" <?php if($form->data["presenter"] == "5"){echo "checked";} ?> />
<label for="id_5author_presenter">発表者</label>
&nbsp;&nbsp;
<?php
if ($form->inputs["5author"])
	{
		echo $form->error_message("presenter");
        }
?>
</div>
</td>
</tr>
<tr id="id_table_5author_note">
<td class="class_text">
第六著者以降は備考欄にお名前、ご所属をご記入ください
</td>
</tr>
<tr>


</tr>

<!--
-->
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
変更される場合は変更ボタンを、このままお申込みされる場合は申込ボタンをクリックしてください。
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
フリガナ
</th>
<td class="class_text">
<?php echo h($form->data["last_name_ruby"]) ?>
&nbsp;
<?php echo h($form->data["first_name_ruby"]) ?> 
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
性別
</th>
<td class="class_text">
<?php
	if ($form->data["gender"] == "1")
	{
		echo "男";
	}else{
		echo "女";
	}
?>
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
		echo "学生または 30 歳未満，"."指導教員または年齢：".$form->data["teacher"];
	}
?>
</td>
</tr>

<tr id="id_confirm_table_award_entry">
<th>
ＯＲ学会からの<br>
補助希望<br>
（学生か <br>
9/7 で 30 歳未満）
</th>
<td class="class_text">
<?php
	if ($form->data["award_entry"] == "1")
	{
		echo "希望する";
	}else{
		echo "希望しない";
	}
?>
</td>
</tr>

<tr>
<th>
領収書
</th>
<td class="class_text">
<?php
	if ($form->data["receipt"] == "1")
	{
		echo "要";
	}else{
		echo "不要";
	}
?>
</td>
</tr>

<tr>
<th>
発表
</th>
<td class="class_text">
<?php
	if ($form->data["presentation"] == "1")
	{
		echo "有";
	}else{
		echo "無";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_presentation_type">
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
		echo "ショート，ロングどちらでも可";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_paper_name">
<th>
題目
</th>
<td class="class_text">
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["paper_name"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_1author_name">
<th rowspan="3">
第一著者
</th>
<td class="class_text">
お名前
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["1author_last_name"])." ".h($form->data["1author_first_name"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_1author_affiliation">
<td class="class_text">
ご所属
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["1author_affiliation"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_1author_presenter">
<td class="class_text">
発表者
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		if ($form->data["presenter"] == "1")
		{
			echo "○";
		}else{
			echo "×";
		}
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_2author_name">
<th rowspan="3">
第二著者
</th>
<td class="class_text">
お名前
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["2author_last_name"])." ".h($form->data["2author_first_name"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_2author_affiliation">
<td class="class_text">
ご所属
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["2author_affiliation"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_2author_presenter">
<td class="class_text">
発表者
&nbsp;
<?php
	if (($form->data["presentation"] == "1") && ($form->inputs["2author"]))
	{
		if ($form->data["presenter"] == "2")
		{
			echo "○";
		}else{
			echo "×";
		}
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_3author_name">
<th rowspan="3">
第三著者
</th>
<td class="class_text">
お名前
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["3author_last_name"])." ".h($form->data["3author_first_name"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_3author_affiliation">
<td class="class_text">
ご所属
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["3author_affiliation"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_3author_presenter">
<td class="class_text">
発表者
&nbsp;
<?php
	if (($form->data["presentation"] == "1") && ($form->inputs["3author"]))
	{
		if ($form->data["presenter"] == "3")
		{
			echo "○";
		}else{
			echo "×";
		}
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_4author_name">
<th rowspan="3">
第四著者
</th>
<td class="class_text">
お名前
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["4author_last_name"])." ".h($form->data["4author_first_name"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_4author_affiliation">
<td class="class_text">
ご所属
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["4author_affiliation"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_4author_presenter">
<td class="class_text">
発表者
&nbsp;
<?php
	if (($form->data["presentation"] == "1") && ($form->inputs["4author"]))
	{
		if ($form->data["presenter"] == "4")
		{
			echo "○";
		}else{
			echo "×";
		}
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_5author_name">
<th rowspan="3">
第五著者
</th>
<td class="class_text">
お名前
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["5author_last_name"])." ".h($form->data["5author_first_name"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_5author_affiliation">
<td class="class_text">
ご所属
&nbsp;
<?php
	if ($form->data["presentation"] == "1")
	{
		echo h($form->data["5author_affiliation"]);
	}else{
		echo "";
	}
?>
</td>
</tr>

<tr id="id_confirm_table_5author_presenter">
<td class="class_text">
発表者
&nbsp;
<?php
	if (($form->data["presentation"] == "1") && ($form->inputs["5author"]))
	{
		if ($form->data["presenter"] == "5")
		{
			echo "○";
		}else{
			echo "×";
		}
	}else{
		echo "";
	}
?>
</td>
</tr>
<!--
<tr>
</tr>
<th>
懇親会
</th>

<td class="class_text">
<?php
	if ($form->data["lang_type"] == "1")
	{
		echo "ご出席";
	}else{
		echo "ご欠席";
	}
?>
</td>

</tr>
<tr>
-->

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
<input type="hidden" name="gender" value="<?php echo h($form->data["gender"]) ?>" />
<input type="hidden" name="attend_type" value="<?php echo $form->data["attend_type"] ?>" />
<input type="hidden" name="teacher" value="<?php echo $form->data["teacher"] ?>" />
<input type="hidden" name="receipt" value="<?php echo $form->data["receipt"] ?>" />

<input type="hidden" name="presentation" value="<?php echo $form->data["presentation"] ?>" />
<input type="hidden" name="presentation_type" value="<?php echo $form->data["presentation_type"] ?>" />
<input type="hidden" name="award_entry" value="<?php echo $form->data["award_entry"] ?>" />

<input type="hidden" name="paper_name" value="<?php echo h($form->data["paper_name"]) ?>" />
<input type="hidden" name="lang_type" value="<?php echo $form->data["lang_type"] ?>" />

<input type="hidden" name="1author_last_name" value="<?php echo h($form->data["1author_last_name"]) ?>" />
<input type="hidden" name="1author_first_name" value="<?php echo h($form->data["1author_first_name"]) ?>" />
<input type="hidden" name="1author_affiliation" value="<?php echo h($form->data["1author_affiliation"]) ?>" />

<input type="hidden" name="2author_last_name" value="<?php echo h($form->data["2author_last_name"]) ?>" />
<input type="hidden" name="2author_first_name" value="<?php echo h($form->data["2author_first_name"]) ?>" />
<input type="hidden" name="2author_affiliation" value="<?php echo h($form->data["2author_affiliation"]) ?>" />

<input type="hidden" name="3author_last_name" value="<?php echo h($form->data["3author_last_name"]) ?>" />
<input type="hidden" name="3author_first_name" value="<?php echo h($form->data["3author_first_name"]) ?>" />
<input type="hidden" name="3author_affiliation" value="<?php echo h($form->data["3author_affiliation"]) ?>" />

<input type="hidden" name="4author_last_name" value="<?php echo h($form->data["4author_last_name"]) ?>" />
<input type="hidden" name="4author_first_name" value="<?php echo h($form->data["4author_first_name"]) ?>" />
<input type="hidden" name="4author_affiliation" value="<?php echo h($form->data["4author_affiliation"]) ?>" />

<input type="hidden" name="5author_last_name" value="<?php echo h($form->data["5author_last_name"]) ?>" />
<input type="hidden" name="5author_first_name" value="<?php echo h($form->data["5author_first_name"]) ?>" />
<input type="hidden" name="5author_affiliation" value="<?php echo h($form->data["5author_affiliation"]) ?>" />

<input type="hidden" name="presenter" value="<?php echo $form->data["presenter"] ?>" />


<input type="hidden" name="notes" value="<?php echo $form->data["notes"] ?>" />

<input type="hidden" name="act" value = "sub">
<input type="submit" class="styled" id="id_sub" value="申込" />

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
<input type="hidden" name="gender" value="<?php echo h($form->data["gender"]) ?>" />
<input type="hidden" id="id_confirm_attend_type" name="attend_type" value="<?php echo $form->data["attend_type"] ?>" />
<input type="hidden" name="teacher" value="<?php echo $form->data["teacher"] ?>" />
<input type="hidden" name="receipt" value="<?php echo $form->data["receipt"] ?>" />

<input type="hidden" id="id_confirm_presentation" name="presentation" value="<?php echo $form->data["presentation"] ?>" />
<input type="hidden" name="presentation_type" value="<?php echo $form->data["presentation_type"] ?>" />
<input type="hidden" name="award_entry" value="<?php echo $form->data["award_entry"] ?>" />

<input type="hidden" name="paper_name" value="<?php echo h($form->data["paper_name"]) ?>" />

<input type="hidden" name="1author_last_name" value="<?php echo h($form->data["1author_last_name"]) ?>" />
<input type="hidden" name="1author_first_name" value="<?php echo h($form->data["1author_first_name"]) ?>" />
<input type="hidden" name="1author_affiliation" value="<?php echo h($form->data["1author_affiliation"]) ?>" />

<input type="hidden" id="id_confirm_2author_last_name" name="2author_last_name" value="<?php echo h($form->data["2author_last_name"]) ?>" />
<input type="hidden" name="2author_first_name" value="<?php echo h($form->data["2author_first_name"]) ?>" />
<input type="hidden" name="2author_affiliation" value="<?php echo h($form->data["2author_affiliation"]) ?>" />

<input type="hidden" id="id_confirm_3author_last_name" name="3author_last_name" value="<?php echo h($form->data["3author_last_name"]) ?>" />
<input type="hidden" name="3author_first_name" value="<?php echo h($form->data["3author_first_name"]) ?>" />
<input type="hidden" name="3author_affiliation" value="<?php echo h($form->data["3author_affiliation"]) ?>" />

<input type="hidden" id="id_confirm_4author_last_name" name="4author_last_name" value="<?php echo h($form->data["4author_last_name"]) ?>" />
<input type="hidden" name="4author_first_name" value="<?php echo h($form->data["4author_first_name"]) ?>" />
<input type="hidden" name="4author_affiliation" value="<?php echo h($form->data["4author_affiliation"]) ?>" />

<input type="hidden" id="id_confirm_5author_last_name" name="5author_last_name" value="<?php echo h($form->data["5author_last_name"]) ?>" />
<input type="hidden" name="5author_first_name" value="<?php echo h($form->data["5author_first_name"]) ?>" />
<input type="hidden" name="5author_affiliation" value="<?php echo h($form->data["5author_affiliation"]) ?>" />

<input type="hidden" name="presenter" value="<?php echo $form->data["presenter"] ?>" />

<input type="hidden" name="lang_type" value="<?php echo $form->data["lang_type"] ?>" />

<input type="hidden" name="notes" value="<?php echo $form->data["notes"] ?>" />

<input type="hidden" name="act" value = "ent">
<input type="submit" class="styled" id="id_ent" value="変更" />

</form>
</td>
</tr>
</table>

<?php
}

/******************************************************************
                           送信完了画面
******************************************************************/
function scr_sub()
{
?>

<h3>参加・発表申込み完了</h3>

<p>
2017年 中国・四国地区ＳＳＯＲへの参加・発表の申込みをしていただきありがとうございました。<br>
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
