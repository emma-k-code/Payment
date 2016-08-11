<?php

/**
 * Class Database
 * 資料庫相關方法
 */
require_once 'Database.php';

/**
 * Class Account
 * 帳戶相關方法
 */
class Account extends Database
{
    /**
     * 搜尋帳戶餘額
     *
     * @param  string $account 帳戶名稱
     *
     * @return string
     */
    public function searchBalance($account)
    {
        $sql = "SELECT `balance` FROM `account` WHERE `account` = :account";
        $result = $this->prepare($sql);
        $result->bindParam('account', $account);
        $result->execute();
        $balance = $result->fetchColumn();

        return $balance;
    }

    /**
     * 搜尋帳戶交易明細
     *
     * @param  string $account 帳戶名稱
     *
     * @return array
     */
    public function searchDetail($account)
    {
        $sql = "SELECT * FROM `details` WHERE `account` = :account " .
        "ORDER BY `datetime` DESC";

        $result = $this->prepare($sql);
        $result->bindParam('account', $account);
        $result->execute();

        return $result->fetchAll();
    }

    /**
     * 將交易寫入資料庫
     *
     * @param  string $io      用來判斷轉入或是轉出
     * @param  string $account 帳戶名稱
     * @param  int    $money   金額
     * @param  string $now     交易時間
     *
     * @return string|null
     */
    public function insertTransaction($io, $account, $money, $now)
    {
        if ($io == 'out') {
            $money = -$money;
        }

        try {
            $this->transaction();


            /*
              樂觀並行控制又名「樂觀鎖」(OCC) 資料來源：wiki
                 在交易提交資料更新前，先檢查在該交易讀取資料後，
                 有沒有其他交易修改了該資料。
                 如果有的話，正在提交的交易會進行回復。
                 樂觀並行控制多數用於資料爭用不大、衝突較少的環境中，
                 這種環境中，偶爾回復交易的成本會低於讀取資料時鎖定資料的成本。
            */
            /* 使用樂觀鎖需要搭配資料庫的transaction、commit與rollback */
            /*
              數據版本
                 進行樂觀鎖的其中一種方式，於DB中增加一個欄位儲存數據版本，
                 在進行資料庫更新前需要先比對DB中的數據版本是否與先前相同，
                 只要在確定相同時才執行更新，否則就取消其關聯的所有資料庫動作。
            */
            $sql = "SELECT * FROM `account` WHERE `account` = :account";
            $result = $this->prepare($sql);
            $result->bindParam('account', $account);
            $result->execute();
            $accountData = $result->fetch();

            $balance = $accountData[1] + $money;
            $version = $accountData[2];

            if ($balance < 0) {
                throw new Exception('餘額不足');
            }

            $sql = "UPDATE `account` SET `balance` = `balance` + :money, " .
            "`version` = :version + 1 WHERE `account` = :account " .
            "AND `version` = :version";
            $sth = $this->prepare($sql);
            $sth->bindParam('account', $account);
            $sth->bindParam('money', $money);
            $sth->bindParam('version', $version);

            if (!$sth->execute()) {
                throw new Exception('交易失敗');
            }

            if ($sth->rowCount() == 0) {
                throw new Exception('交易失敗');
            }

            $sql = "INSERT INTO " .
            "`details`(`account`, `datetime`, `transaction`)" .
            "VALUES (:account, :now, :money)";
            $sth = $this->prepare($sql);
            $sth->bindParam('account', $account);
            $sth->bindParam('now', $now);
            $sth->bindParam('money', $money);

            if (!$sth->execute()) {
                throw new Exception('交易失敗');
            }

            $this->commit();
        } catch(Exception $e) {
            $this->rollBack();
            $error = $e->getMessage();

            return $error;
        }
    }
}