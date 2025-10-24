<?php namespace App\Libraries;

use Config\Services;

class Flangapp
{
    private ?string $licenseKey;
    private string $apiUrl;

    /**
     * Create models, config and library's
     */
    function __construct()
    {
        $settings = new Settings();
        $this->licenseKey = $settings->get_config("license");
        $this->apiUrl = "https://builder.flangapp.com/";
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * License activation
     * @param string $code
     * @return array
     */
    public function activation_license(string $code): array
    {
        $res = Services::curlrequest([
            "baseURI"     => $this->apiUrl,
            "headers"     => [
                "Content-Type" => "application/json",
                "User-Agent"   => base_url()
            ],
            "http_errors" => false,
        ])->setJSON(["code" => $code])->post("public/license/activation");

        $data =  json_decode($res->getBody());

        if ($res->getStatusCode() != 200) {
            return [
                "event"   => false,
                "message" => !empty($data->message) ? $data->message : lang("Message.message_90")
            ];
        }

        return ["event" => true];
    }

    /**
     * Get snack project preview files
     * @return array
     */
    public function get_snacks(): array
    {
        $res = Services::curlrequest([
            "baseURI"     => $this->apiUrl,
            "headers"     => [
                "Content-Type"   => "application/json",
                "Authorization"  => $this->licenseKey
            ],
            "http_errors" => false,
        ])->post("private/preview/snack");

        $data =  json_decode($res->getBody());

        if ($res->getStatusCode() != 200) {
            return [
                "event"   => false,
                "message" => !empty($data->message) ? $data->message : lang("Message.message_82")
            ];
        }

        return ["event" => true, "data" => $data];
    }

    /**
     * Create android keystore without file upload
     * @param string $alias
     * @param string $password
     * @return array
     */
    public function create_keystore(string $alias, string $password): array
    {
        helper('text');

        $res = Services::curlrequest([
            "baseURI"     => $this->apiUrl,
            "headers"     => [
                "Content-Type"   => "application/json",
                "Authorization"  => $this->licenseKey
            ],
            "http_errors" => false,
        ])->setJSON([
            "alias" => $alias,
            "password" => $password
        ])->post("private/signature/android");

        if ($res->getStatusCode() != 200) {
            $data =  json_decode($res->getBody());
            return [
                "event"   => false,
                "message" => !empty($data->message) ? $data->message : lang("Message.message_70")
            ];
        }

        $name = random_string("alpha", 10).".jks";

        $fp = fopen(WRITEPATH.'storage/android/'.$name,"wb");
        fwrite($fp, $res->getBody());
        fclose($fp);

        return ["event" => true, "name" => $name];
    }

    /**
     * Create .pem file for ios signature
     * @return array
     */
    public function create_pem(): array
    {
        helper('text');

        $res = Services::curlrequest([
            "baseURI"     => $this->apiUrl,
            "headers"     => [
                "Content-Type"   => "application/json",
                "Authorization"  => $this->licenseKey
            ],
            "http_errors" => false,
        ])->post("private/signature/pem");

        if ($res->getStatusCode() != 200) {
            $data =  json_decode($res->getBody());
            return [
                "event"   => false,
                "message" => !empty($data->message) ? $data->message : lang("Message.message_70")
            ];
        }

        $name = random_string("alpha", 10);

        $fp = fopen(WRITEPATH.'storage/pub/'.$name,"wb");
        fwrite($fp, $res->getBody());
        fclose($fp);

        return ["event" => true, "name" => $name];
    }
}