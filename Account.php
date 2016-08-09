<?php

/**
 * Class Database
 *     資料庫相關方法
 */
require_once "Database.php";

class Account extends Database
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
            
            /*  悲觀並行控制又名「悲觀鎖」(PPC)
                    為了阻止一個交易會影響其他用戶修改資料，在交易執行時將某行資料鎖起來，
                    只有當該交易將鎖解開時，其他交易才能執行。
            */
            /*  MySQL中要使用悲觀鎖需先將MySQL設置為非autocommit模式
                    set autocommit=0;
                之後搭配transaction與commit
            */
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
            
        } catch(Exception $e) {
            $this->rollBack();
            $error = $e->getMessage();
        }
        
        return $error;
    }
    
}
