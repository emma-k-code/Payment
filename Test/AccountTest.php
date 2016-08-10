<?php

require_once "Payment/Account.php";

class AccountTest extends PHPUnit_Framework_TestCase
{
    public function providerTestSearch()
    {
        return [["emma", "2000"]];
    }

    public function providerTestSearchDetail()
    {
        return [["emma", 4032], ["test", 33]];
    }

    public function providerTestInsert()
    {
        return [["out","emma", "100", "2016-08-10 11:55:00", null],
                ["enter","emma", "400", "2016-08-10 11:55:01", null]];
    }

    /**
     * @param  string  $accountName
     * @param  string  $balance
     *
     * @dataProvider providerTestSearch
     */
    public function testSearch($accountName, $balance)
    {
        $account = new Account;
        $accountBalance = $account->search($accountName);

        $this->assertEquals($accountBalance, $balance);
    }

    /**
     * @param  string  $accountName
     * @param  int     $count
     *
     * @dataProvider providerTestSearchDetail
     */
    public function testSearchDetail($accountName, $count)
    {
        $account = new Account;
        $accountDetail = $account->searchDetail($accountName);

        $this->assertCount($count, $accountDetail);
    }

    /**
     * @param  string  $io
     * @param  string  $accountName
     * @param  string  $money
     * @param  string  $time
     * @param  string  $result
     *
     * @dataProvider providerTestInsert
     */
    public function testInsert($io, $accountName, $money, $time, $result)
    {
        $account = new Account;
        $insertResult = $account->insert($io, $accountName, $money, $time);

        $this->assertEquals($insertResult, $result);
    }
}
