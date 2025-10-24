<?php
namespace App\Controllers\Api\Data;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Iso extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get languages (ISO codes)
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $jsonString = file_get_contents(WRITEPATH."/data/iso.json");
        $dataArray = json_decode($jsonString, true);
        return $this->respond($dataArray, 200);
    }
}