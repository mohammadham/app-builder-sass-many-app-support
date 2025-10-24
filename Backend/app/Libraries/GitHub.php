<?php namespace App\Libraries;

use Config\Services;
use Exception;

class GitHub
{
    private ?string $token;
    private ?string $username;
    private ?string $repo;
    private string $branchName;
    private string $baseApiUrl;
    private string $apiVersion;
    private string $templateOwner;
    private string $templateName;

    /**
     * Create models, config and library's
     */
    function __construct()
    {
        $settings = new Settings();
        $this->token = $settings->get_config("github_token");
        $this->username = $settings->get_config("github_username");
        $this->repo = $settings->get_config("github_repo");
        $this->branchName = $settings->get_config("github_branch");
        $this->baseApiUrl = "https://api.github.com/";
        $this->apiVersion = "2022-11-28";
        $this->templateOwner = "sitenativedev";
        $this->templateName = "flangapp_pro_starter";
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Create fork
     * @param string $token
     * @param string $user
     * @return array
     */
    public function create_fork(string $token, string $user): array
    {
        try {
            $res = Services::curlrequest([
                "baseURI"     => $this->baseApiUrl,
                "headers"     => [
                    "Content-Type"         => "application/json",
                    "X-GitHub-Api-Version" => $this->apiVersion,
                    "User-Agent"           => "Flangapp PRO API Server",
                    "Authorization"        => "Bearer ".$token
                ],
            ])->setJSON([
                "owner"   => $user,
                "name"    => "flangapp_pro",
                "private" => true
            ])->post("repos/".$this->templateOwner."/".$this->templateName."/generate");
            $data = json_decode($res->getBody());
            if (!empty($data->id)) {
                return ['event' => true];
            } else {
                return ['event' => false, 'message' => lang("Message.message_98")];
            }
        } catch (Exception $e) {
            return ['event' => false, 'message' => lang("Message.message_98")];
        }
    }

    /**
     * Create new branch for new app
     * @param string $name
     * @return array
     */
    public function create_branch(string $name): array
    {
        $target = $this->get_sha_repo();

        if (!$target["event"]) {
            return ['event' => false, 'message' => lang("Message.message_25")];
        }

        try {
            $res = Services::curlrequest([
                "baseURI"     => $this->baseApiUrl,
                "headers"     => [
                    "Content-Type"         => "application/json",
                    "X-GitHub-Api-Version" => $this->apiVersion,
                    "User-Agent"           => "Flangapp PRO API Server",
                    "Authorization"        => "Bearer ".$this->token,
                ],
            ])->setJSON([
                "ref" => "refs/heads/".$name,
                "sha" => $target['sha']
            ])->post("repos/".$this->username."/".$this->repo."/git/refs");
            $data = json_decode($res->getBody());
            if (!empty($data->ref)) {
                return ['event' => true];
            } else {
                return ['event' => false, 'message' => lang("Message.message_26")];
            }
        } catch (Exception $e) {
            return ['event' => false, 'message' => lang("Message.message_26")];
        }
    }

    /**
     * Create new commit
     * @param string $branch
     * @param string $path
     * @param $content
     * @return array
     */
    public function create_commit(string $branch, string $path, $content): array
    {
        $hash = $this->get_sha_file($path, $branch);

        if (!$hash["event"]) {
            return ["event" => false, "message" => lang("Message.message_28").": ".$path];
        }

        try {
            $res = Services::curlrequest([
                "baseURI"     => $this->baseApiUrl,
                "headers"     => [
                    "Content-Type"         => "application/json",
                    "X-GitHub-Api-Version" => $this->apiVersion,
                    "User-Agent"           => "Flangapp PRO API Server",
                    "Authorization"        => "Bearer ".$this->token,
                ],
            ])->setJSON([
                "message" => "update for file ".$path." - ".date('d-m-Y H:i'),
                "branch"  => $branch,
                "content" => base64_encode($content),
                "sha"     => $hash["sha"],
            ])->put("repos/".$this->username."/".$this->repo."/contents/".$path);
            $data = json_decode($res->getBody());
            if (!empty($data->content)) {
                return ['event' => true];
            } else {
                return ['event' => false, 'message' => lang("Message.message_27").": ".$path];
            }
        } catch (Exception $e) {
            print_r($e);
            return ['event' => false, 'message' => lang("Message.message_27").": ".$path];
        }
    }

    /**
     * Delete file
     * @param string $branch
     * @param string $path
     * @return array
     */
    public function delete_file(string $branch, string $path): array
    {
        $hash = $this->get_sha_file($path, $branch);
        if (!$hash["event"]) {
            return [
                'event'   => false,
                'message' => lang("Message.message_28"),
            ];
        }
        $data = [
            "message" => "delete file ".$path,
            "branch"  => $branch,
            "path"    => $path,
            "sha"     => $hash["sha"]
        ];
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, 'https://api.github.com/repos/'.$this->username.'/'.$this->repo.'/contents/'.$path);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_HTTPHEADER, [
            "authorization: token $this->token",
            "User-Agent: SiteNative Server"
        ]);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
        $final = curl_exec($handle);
        $response = json_decode($final, true);
        if (!empty($response['commit'])) {
            return ['event' => true];
        } else {
            return [
                'event'   => false,
                'message' => lang("Message.message_33")
            ];
        }
    }

    /**
     * Upload new file
     * @param string $branch
     * @param string $path
     * @param $content
     * @return array
     */
    public function upload_commit(string $branch, string $path, $content): array
    {
        $data = [
            "message" => "update for file ".$path,
            "branch"  => $branch,
            "content" => base64_encode($content),
            "path"    => $path
        ];
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, 'https://api.github.com/repos/'.$this->username.'/'.$this->repo.'/contents/'.$path);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_HTTPHEADER, [
            "authorization: token $this->token",
            "User-Agent: SiteNative Server"
        ]);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
        $final = curl_exec($handle);
        $response = json_decode($final, true);
        if (!empty($response['commit'])) {
            return ['event' => true];
        } else {
            return [
                'event'   => false,
                'message' => lang("Message.message_27")
            ];
        }
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get Sha for file
     * @param string $path
     * @param string $branch
     * @return array|false[]
     */
    private function get_sha_file(string $path, string $branch): array
    {
        try {
            $res = Services::curlrequest([
                "baseURI"     => $this->baseApiUrl,
                "headers"     => [
                    "Content-Type"         => "application/json",
                    "X-GitHub-Api-Version" => $this->apiVersion,
                    "User-Agent"           => "Flangapp PRO API Server",
                    "Authorization"        => "Bearer ".$this->token,
                ],
            ])->get("repos/".$this->username."/".$this->repo."/contents/".$path."?ref=".$branch);
            $data = json_decode($res->getBody());
            if (!empty($data->sha)) {
                return ["event" => true, "sha" => $data->sha];
            } else {
                return ["event" => false];
            }
        } catch (Exception $e) {
            return ["event" => false];
        }
    }

    /**
     * Get Sha hash for repo
     * @return array|false[]
     */
    private function get_sha_repo(): array
    {
        try {
            $res = Services::curlrequest([
                "baseURI"     => $this->baseApiUrl,
                "headers"     => [
                    "Content-Type"         => "application/json",
                    "X-GitHub-Api-Version" => $this->apiVersion,
                    "User-Agent"           => "Flangapp PRO API Server",
                    "Authorization"        => "Bearer ".$this->token,
                ],
            ])->get("repos/".$this->username."/".$this->repo."/branches/".$this->branchName);
            $data = json_decode($res->getBody());
            if (!empty($data->commit)) {
                return ["event" => true, "sha" => $data->commit->sha];
            } else {
                return ["event" => false];
            }
        } catch (Exception $e) {
            return ["event" => false];
        }
    }
}