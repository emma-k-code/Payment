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
        $result->bindParam("account", $account);
        $result->execute();
        
        return $result->fetch();
    }
    
    public function searchDetail($account)
    {
        $sql = "SELECT * FROM `details` WHERE `account` = :account ORDER BY `datetime` DESC";
        $result = $this->prepare($sql);
        $result->bindParam("account", $account);
        $result->execute();
        
        return $result->fetchAll();
    }
    
    public function insert($io, $account, $money, $now)
    {
        if ($io == "out") {
            $money = -$money;
        }
        
        try {
            $this->transaction();
            
            $sql = "SELECT * FROM `account` WHERE `account` = :account FOR UPDATE";
            $result = $this->prepare($sql);
            $result->bindParam("account", $account);
            $result->execute();
            
            $accountData = $result->fetch();
            
            if (($accountData[1] + $money) < 0) {
                throw new Exception("餘額不足");
            }

            $sql = "INSERT INTO `details`(`account`, `datetime`, `transaction`) VALUES (:account, :now, :money)";
            $sth = $this->prepare($sql);
            $sth->bindParam("account", $account);
            $sth->bindParam("now", $now);
            $sth->bindParam("money", $money);
            if (!$sth->execute()) {
                throw new Exception("交易失敗");
            }
        
            $sql = "UPDATE `account` SET `balance` = `balance` + (:money) WHERE `account` = :account";
            $sth = $this->prepare($sql);
            $sth->bindParam("account", $account);
            $sth->bindParam("money", $money);
            if (!$sth->execute()) {
                throw new Exception("交易失敗");
            }
            
            $this->commit();
            
        }catch(Exception $e) {
            $this->rollBack();
            $error = $e->getMessage();
        }
        
        return $error;
    }
}

?>
