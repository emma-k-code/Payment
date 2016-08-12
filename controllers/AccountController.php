<?php

/**
 * Class AccountController
 * 帳戶相關的Controller
 */
class AccountController extends Controller
{
    /**
     * 顯示帳戶輸入畫面
     */
    public function index()
    {
        $this->view('index');
    }

    /**
     * 顯示帳戶轉帳與交易明細畫面
     */
    public function detail()
    {
        $accountName = addslashes($_POST['account']);

        $account = $this->model('Account');

        if (isset($_POST['enterSubmit'])) {
            $money = addslashes($_POST['enter']);
            $error = $account->insertTransaction(
                                    'enter',
                                    $accountName,
                                    $money
                                );
        }

        if (isset($_POST['outSubmit'])) {
            $money = addslashes($_POST['out']);
            $error = $account->insertTransaction(
                                    'out',
                                    $accountName,
                                    $money
                                );
        }

        $balance = $account->searchBalance($accountName);
        if ($balance == null) {
            $accountName =  '無該帳戶';
        }

        $detailtData = $account->searchDetail($accountName);
        $output = [$accountName, $balance, $detailtData, $error];

        $this->view('detail', $output);
    }
}
