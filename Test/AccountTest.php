<?php

require_once "Payment/Account.php";

class AccountTest extends PHPUnit_Framework_TestCase
{
    public function testSearch()
    {
        $accountName = "emma";
        $balance = "2300";

        $account = new Account;
        $accountBalance = $account->searchBalance($accountName);

        $this->assertEquals($accountBalance, $balance);
    }

    public function testSearchDetail()
    {
        $accountName = "emma";
        $count = 4034;

        $account = new Account;
        $accountDetail = $account->searchDetail($accountName);

        $this->assertCount($count, $accountDetail);
    }

    public function testInsert()
    {
        $io = "out";
        $accountName = "emma";
        $money = "300";
        $time = "2016-08-10 16:55:00";
        $result = null;

        $account = new Account;
        $insertResult = $account->insertTransaction(
                            $io,
                            $accountName,
                            $money,
                            $time
                        );

        $this->assertEquals($insertResult, $result);
    }
}
