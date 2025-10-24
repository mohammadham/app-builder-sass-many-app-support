<?php namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\URI;
use Config\App;

class CodeMagic
{
    private string $apiUrl;
    private ?string $authToken;

    /**
     * Create models, config and library's
     */
    function __construct()
    {
        $settings = new Settings();
        $this->apiUrl = "https://api.codemagic.io/";
        $this->authToken = $settings->get_config("codemagic_key");
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create artefact download link
     * @param string $link
     * @return array
     */
    public function signing_download_link(string $link): array
    {
        $options = [
            "headers"     => [
                "Content-Type"   => "application/json",
                "accept"         => "text/plain",
                "x-auth-token"   => $this->authToken
            ],
            "http_errors" => false,
        ];

        $client = new CURLRequest(
            new App(),
            new URI(),
            new Response(new App()),
            $options
        );

        $res = $client
            ->setJSON(["expiresAt" => time() + 1800])
            ->post($link."/public-url");

        if ($res->getStatusCode() != 200) {
            return [
                "event"   => false,
                "message" => lang("Message.message_80")
            ];
        }

        $data = json_decode($res->getBody());

        return ["event" => true, "url" => $data->url];
    }

    /**
     * Create new build
     * @param string $app_uid
     * @param string $platform
     * @return array
     */
    public function create_build(string $app_uid, string $platform): array
    {
        $options = [
            "baseURI"     => $this->apiUrl,
            "headers"     => [
                "Content-Type"   => "application/json",
                "accept"         => "text/plain",
                "x-auth-token"   => $this->authToken
            ],
            "http_errors" => false,
        ];

        $client = new CURLRequest(
            new App(),
            new URI(),
            new Response(new App()),
            $options
        );

        $settings = new Settings();

        $res = $client->setJSON([
            "appId"      => $settings->get_config("codemagic_id"),
            "workflowId" => $platform == "android" ? "android-workflow" : "ios-workflow",
            "branch"     => $app_uid
        ])->post("builds");

        if ($res->getStatusCode() != 200) {
            return [
                "event"   => false,
                "message" => lang("Message.message_45")
            ];
        }

        $data = json_decode($res->getBody());

        return ["event" => true, "id" => $data->buildId];
    }

    /**
     * Check build status
     * @param string $build_uid
     * @return array
     */
    public function check_status(string $build_uid): array
    {
        $options = [
            "baseURI"     => $this->apiUrl,
            "headers"     => [
                "Content-Type"   => "application/json",
                "accept"         => "text/plain",
                "x-auth-token"   => $this->authToken
            ],
            "http_errors" => false,
        ];

        $client = new CURLRequest(
            new App(),
            new URI(),
            new Response(new App()),
            $options
        );

        $res = $client->get("builds/".$build_uid);

        if ($res->getStatusCode() != 200) {
            return [
                "event"   => false,
                "message" => lang("Message.message_77")
            ];
        }

        $data = json_decode($res->getBody());

        return [
            "event"  => true,
            "data" => $data->build->artefacts
        ];
    }
}