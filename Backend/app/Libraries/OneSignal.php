<?php namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\URI;
use Config\App;
use Config\Services;

class OneSignal
{
    private string $apiUrl;
    private ?string $authKey;
    private ?string $fcmFile;

    /**
     * Create models, config and library's
     */
    function __construct()
    {
        $settings = new Settings();
        $this->apiUrl = "https://api.onesignal.com/";
        $this->authKey = $settings->get_config("one_signal_auth_key");
        $this->fcmFile = $settings->get_config("one_signal_fcm_file");
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get total users
     * @param string $onesignal_app_id
     * @param string $rest_key
     * @param int $offset
     * @return array
     */
    public function get_notifications(string $onesignal_app_id, string $rest_key, int $offset): array
    {
        $options = [
            "baseURI"     => $this->apiUrl,
            "headers"     => [
                "Content-Type"   => "application/json",
                "accept"         => "text/plain",
                "Authorization"  => "Basic ".$rest_key
            ],
            "http_errors" => false,
        ];

        $client = new CURLRequest(
            new App(),
            new URI(),
            new Response(new App()),
            $options
        );

        $res = $client->get("notifications?app_id=".$onesignal_app_id."&limit=20&offset=".$offset);

        if ($res->getStatusCode() != 200) {
            return [
                "event"   => false,
                "message" => lang("Message.message_53")
            ];
        }

        $data = json_decode($res->getBody());

        return ["event" => true, "data" => $data];
    }

    /**
     * Get total users
     * @param string $onesignal_app_id
     * @return array
     */
    public function get_total_users(string $onesignal_app_id): array
    {
        $options = [
            "baseURI"     => $this->apiUrl,
            "headers"     => [
                "Content-Type"   => "application/json",
                "accept"         => "text/plain",
                "Authorization"  => "Basic ".$this->authKey
            ],
            "http_errors" => false,
        ];

        $client = new CURLRequest(
            new App(),
            new URI(),
            new Response(new App()),
            $options
        );

        $res = $client->get("apps/".$onesignal_app_id);

        if ($res->getStatusCode() != 200) {
            return [
                "event"   => false,
                "message" => lang("Message.message_53")
            ];
        }

        $data = json_decode($res->getBody());

        return [
            "event" => true,
            "players" => $data->players,
            "messageable_players" => $data->messageable_players
        ];
    }

    /**
     * Create newsletter
     * @param array $data
     * @param string $rest_key
     * @param string $app_id
     * @return array
     */
    public function create_newsletter(array $data, string $rest_key, string $app_id): array
    {
        $options = [
            "baseURI"     => "https://onesignal.com/api/v1/",
            "headers"     => [
                "Content-Type"   => "application/json",
                "accept"         => "application/json",
                "Authorization"  => "Basic ".$rest_key
            ],
            "http_errors" => false,
        ];

        $client = new CURLRequest(
            new App(),
            new URI(),
            new Response(new App()),
            $options
        );

        $res = $client->setJSON([
            "app_id"            => $app_id,
            "included_segments" => ["All"],
            "contents"          => [
                "en" => $data["content"]
            ],
            "headings"          => [
                "en" => $data["heading"]
            ],
            "large_icon"        => $data["large_icon"],
            "big_picture"       => !$data["big_picture"] ? "" : $data["big_picture"]
        ])->post("notifications");

        if ($res->getStatusCode() != 200) {
            return [
                "event"   => false,
                "message" => lang("Message.message_55")
            ];
        }

        return ["event" => true];
    }

    /**
     * Create Onesignal app
     * @param string $name
     * @return array
     */
    public function create_onesignal_app(string $name): array
    {
        $settings = new Settings();

        $fcmJson = file_get_contents(WRITEPATH."storage/fcm/".$this->fcmFile);

        $options = [
            "baseURI"     => $this->apiUrl,
            "headers"     => [
                "Content-Type"   => "application/json",
                "accept"         => "text/plain",
                "Authorization"  => "Basic ".$this->authKey
            ],
            "http_errors" => false,
        ];

        $client = new CURLRequest(
            new App(),
            new URI(),
            new Response(new App()),
            $options
        );

        $res = $client->setJSON([
            "name"                        => $name,
            "organization_id"             => $settings->get_config("one_signal_organization_id"),
            "fcm_v1_service_account_json" => base64_encode($fcmJson)
        ])->post("apps");

        if ($res->getStatusCode() != 200) {
            return [
                "event"   => false,
                "message" => lang("Message.message_78")
            ];
        }

        $data = json_decode($res->getBody());

        return [
            "event"  => true,
            "app_id" => $data->id,
            "data"   => $data
        ];
    }
}