<?php

require_once 'Payment/Account.php';

class AccountTest extends PHPUnit_Framework_TestCase
{
    public function testSearchBalance()
    {
        $accountName = 'test';
        $balance = '1000';

        $account = new Account;
        $accountBalance = $account->searchBalance($accountName);

        $this->assertEquals($accountBalance, $balance);
    }

    public function testSearchDetail()
    {
        $accountName = 'test';
        $count = 14;

        $account = new Account;
        $accountDetail = $account->searchDetail($accountName);

        $this->assertCount($count, $accountDetail);
    }

    public function testInsertTransactionFail()
    {
        $io = 'out';
        $accountName = 'test';
        $money = '1001';
        $time = '2016-08-12 10:55:00';
        $result = '餘額不足';

        $account = new Account;
        $insertResult = $account->insertTransaction(
                            $io,
                            $accountName,
                            $money,
                            $time
                        );

        $this->assertEquals($insertResult, $result);
    }

    public function testInsertTransactionEnter()
    {
        $io = 'enter';
        $accountName = 'test';
        $money = '200';
        $time = '2016-08-12 10:55:00';
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

    public function testInsertTransactionOut()
    {
        $io = 'out';
        $accountName = 'test';
        $money = '100';
        $time = '2016-08-12 10:55:00';
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
