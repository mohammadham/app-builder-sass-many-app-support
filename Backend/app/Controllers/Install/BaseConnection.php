<?php namespace App\Controllers\Install;

use App\Controllers\BaseController;
use App\Libraries\Uid;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;
use mysqli;

class BaseConnection extends BaseController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create ENV and connect database
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        if (!$this->validate($this->mysql_validation_type())) {
            return $this->respond(["message" => $this->validator->getErrors()], 400);
        }

        $hostname = esc($this->request->getJsonVar("hostname"));
        $username = esc($this->request->getJsonVar("username"));
        $password = esc($this->request->getJsonVar("password"));
        $name     = esc($this->request->getJsonVar("name"));
        $port     = (int) $this->request->getJsonVar("port");
        $url      =  esc($this->request->getJsonVar("url"));

        if (substr($url, -1) !== '/') {
            $url .= '/';
        }

        try {
            $mysqli = new mysqli($hostname, $username, $password, $name, $port);
            $sql = file_get_contents( WRITEPATH.'install/db.sql');
            $mysqli->multi_query($sql);
        } catch (Exception $e ) {
            return $this->respond(["message" => lang("Message.message_64").": ".$e->getMessage()], 400);
        }

        helper('filesystem');

        $uid = new Uid();

        $fileVariables = [
            '{BASE_URL}',
            '{DB_HOSTNAME}',
            '{DB_NAME}',
            '{DB_USER}',
            '{DB_PASSWORD}',
            '{DB_PORT}',
            '{ENCRYPTION_KEY}',
            '{JWT_ACCESS}',
            '{JWT_REFRESH}',
        ];
        $codeVariable = [
            $url,
            $hostname,
            $name,
            $username,
            $password,
            $port,
            hash('sha256', $uid->create()),
            hash('sha256', $uid->create()),
            hash('sha256', $uid->create()),
        ];

        $content = str_replace(
            $fileVariables,
            $codeVariable,
            file_get_contents(WRITEPATH.'install/.env')
        );

        write_file(ROOTPATH.'.env', $content);

        return $this->respond(["status" => "ok"], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get validation rules for mysql database
     * @return array
     */
    private function mysql_validation_type(): array
    {
        return [
            "name"     => [
                "label" => lang("Install.install_4"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "hostname" => [
                "label" => lang("Install.install_5"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "username" => [
                "label" => lang("Install.install_6"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "password" => [
                "label" => lang("Install.install_7"),
                "rules" => "required|min_length[3]|max_length[100]"
            ],
            "port"     => [
                "label" => lang("Install.install_8"),
                "rules" => "required|numeric"
            ],
            "url"      => [
                "label" => lang("Install.install_29"),
                "rules" => "required|min_length[3]|max_length[100]|valid_url_strict"
            ],
        ];
    }
}