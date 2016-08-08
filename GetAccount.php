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
    
    public function insert($io, $account, $money, $now)
    {
        if ($io == "out") {
            $money = -$money;
        }
        
        $sql = "INSERT INTO `details`(`account`, `datetime`, `transaction`) VALUES (:account, :now, :money)";
        $sth = $this->prepare($sql);
        $sth->bindParam("account",$account);
        $sth->bindParam("now",$now);
        $sth->bindParam("money",$money);
        $sth->execute();
        
        $sql = "UPDATE `account` SET `balance` = `balance` + (:money) WHERE `account` = :account";
        $sth = $this->prepare($sql);
        $sth->bindParam("account",$account);
        $sth->bindParam("money",$money);
        $sth->execute();
        
    }
}

?>
