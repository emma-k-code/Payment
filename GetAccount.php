<?php

/**
 * Class Database
 *     資料庫相關方法
 */
require_once "Database.php";

class GetAccount extends Database
{
    public function search($account)
    {
        $sql = "SELECT * FROM `account` WHERE `account` = :account";
        $result = $this->prepare($sql);
        $result->bindParam("account",$account);
        $result->execute();
        
        return $result->fetch();
    }
}

?>
