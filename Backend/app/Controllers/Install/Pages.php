<?php namespace App\Controllers\Install;

use App\Controllers\BaseController;

class Pages extends BaseController
{
    /**
     * Step 1. MySQL's connection and ENV file create
     */
    public function step_1()
    {
        helper('filesystem');
        $env = get_file_info(ROOTPATH.'.env');
        if (!empty($env)) {
            return redirect()->to('install/step/2');
        }
        $dataPage = [

        ];
        return view('install/step_1', $dataPage);
    }

    /**
     * Step 2. External api settings
     * @return string
     */
    public function step_2(): string
    {
        $dataPage = [

        ];
        return view('install/step_2', $dataPage);
    }

    /**
     * Step 3. Create admin account
     * @return string
     */
    public function step_3(): string
    {
        $dataPage = [

        ];
        return view('install/step_3', $dataPage);
    }

    /**
     * Step 4. Finish notification
     * @return string
     */
    public function step_4(): string
    {
        $dataPage = [

        ];
        return view('install/step_4', $dataPage);
    }
}