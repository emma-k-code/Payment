<?php

if (isset($_POST['submit'])) {
    $account = addslashes($_POST['account']);
    require_once "GetAccount.php";
    $getData = new GetAccount;
    $accountData = $getData->search($account);
}
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
</form>

<div>
    <!--顯示交易明細-->
</div>