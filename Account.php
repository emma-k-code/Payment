<?php
/**
 * Class Database
 *     資料庫相關方法
 */
require_once "Database.php";
/**
 * Class Account
 *     帳戶相關方法
 */
class Account extends Database
{
    /**
     * 搜尋帳戶餘額
     *
     * @param   string  $account  帳戶名稱
     * @return  string
     */
    public function searchBalance($account)
    {
        $sql = "SELECT `balance` FROM `account` WHERE `account` = :account";
        $result = $this->prepare($sql);
        $result->bindParam("account", $account);
        $result->execute();
        $balance = $result->fetchColumn();

        return $balance;
    }

    /**
     * 搜尋帳戶交易明細
     *
     * @param   string  $account  帳戶名稱
     * @return  array
     */
    public function searchDetail($account)
    {
        $sql = "SELECT * FROM `details` WHERE `account` = :account
        ORDER BY `datetime` DESC";

        $result = $this->prepare($sql);
        $result->bindParam("account", $account);
        $result->execute();

        return $result->fetchAll();
    }

    /**
     * 將交易寫入資料庫
     *
     * @param   string  $io       用來判斷轉入或是轉出
     * @param   string  $account  帳戶名稱
     * @param   int     $money    金額
     * @param   string  $now      交易時間
     * @return  string
     */
    public function insertTransaction($io, $account, $money, $now)
    {

        if ($io == "out") {
            $money = -$money;
        }

        try {
            $this->transaction();
            $sql = "SELECT * FROM `account` WHERE `account` = :account";
            $result = $this->prepare($sql);
            $result->bindParam("account", $account);
            $result->execute();
            $accountData = $result->fetch();

            $balance = $accountData[1] + $money;
            $version = $accountData[2];

            if ($balance < 0) {
                throw new Exception("餘額不足");
            }

            $sql =
            "UPDATE `account` SET `balance` = :balance,`version` = :version+1
            WHERE `account` = :account AND `version` = :version";
            $sth = $this->prepare($sql);
            $sth->bindParam("account", $account);
            $sth->bindParam("balance", $balance);
            $sth->bindParam("version", $version);

            if (!$sth->execute()) {
                throw new Exception("交易失敗");
            }

            if ($sth->rowCount() == 0) {
                throw new Exception("交易失敗");
            }

            $sql = "INSERT INTO `details`(`account`, `datetime`, `transaction`)
            VALUES (:account, :now, :money)";
            $sth = $this->prepare($sql);
            $sth->bindParam("account", $account);
            $sth->bindParam("now", $now);
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