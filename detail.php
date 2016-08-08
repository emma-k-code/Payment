<?php

require_once "GetAccount.php";

date_default_timezone_set("Asia/Taipei");
$now = date('Y-m-d H:i:s');
$account = addslashes($_POST['account']);

$getData = new GetAccount;

if (isset($_POST['enterSubmit'])) {
    $money = addslashes($_POST['enter']);
    $getData->insert("enter", $account, $money, $now);
}

if (isset($_POST['outSubmit'])) {
    $money = addslashes($_POST['out']);
    $getData->insert("out", $account, $money, $now);
}

$accountData = $getData->search($account);

?>
<meta charset="utf-8">
<h2><?php echo $accountData[0];?></h2>
<h2>現在餘額：<?php echo $accountData[1];?></h2>
<form method="POST">
    <label for="enter">輸入金額：</label>
    <input type="number" id="enter" name="enter"/>
    <input type="submit" min="0" name="enterSubmit" value="轉入"/>
    <br>
    <br>
    <label for="out">輸入金額：</label>
    <input type="number" id="out" name="out"/>
    <input type="submit" min="0" name="outSubmit" value="轉出"/>
    
    <input type="hidden" name="account" value="<?php echo $accountData[0];?>"/>
</form>

<div>
    <!--顯示交易明細-->
</div>