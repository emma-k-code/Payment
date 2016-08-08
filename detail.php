<?php
header("content-type: text/html; charset=utf-8");
?>
<h2>現在餘額：</h2>
<form method="POST">
    <label for="enter">輸入金額：</label>
    <input type="number" id="enter" name="enter"/>
    <input type="submit" min="0" name="submit" value="轉入"/>
    <br>
    <br>
    <label for="out">輸入金額：</label>
    <input type="number" id="out" name="out"/>
    <input type="submit" min="0" name="submit" value="轉出"/>
</form>

<div>
    <!--顯示交易明細-->
</div>