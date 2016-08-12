<?php

/**
 * Class Controller
 */
class Controller
{
    /**
     * require models資料夾中的檔案並回傳一個物件
     *
     * @param string $model 檔案名稱
     *
     * @return object
     */
    public function model($model)
    {
        require_once "../payment_MVC/models/$model.php";

        return new $model ();
    }

    /**
     * require views資料夾中的檔案
     *
     * @param string $model 檔案名稱
     * @param array  $data  欲顯示的內容
     */
    public function view($view, $data = array())
    {
        require_once "../payment_MVC/views/$view.php";
    }
}
