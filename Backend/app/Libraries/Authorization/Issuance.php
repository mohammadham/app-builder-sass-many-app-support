<?php namespace App\Libraries\Authorization;

class Issuance
{
    /**
     * Create JWT tokens
     * @param string $userId
     * @return array
     */
    public function create_auth_tokens(string $userId): array
    {
        $time = strtotime(date("d-m-Y H:i:s"));
        $payload_a = [
            "iss"  => env("jwt.site.issuer"),
            "aud"  => env("jwt.site.audience"),
            "iat"  => $time,
            "nbf"  => $time,
            "exp"  => strtotime("+".env("jwt.time.exp.access")." minutes", $time),
            "type" => "access",
            "user" => (int) $userId
        ];
        $access = JWT::encode($payload_a, env("jwt.secret.key.access"));
        $payload_b = [
            "iss"  => env("jwt.site.issuer"),
            "aud"  => env("jwt.site.audience"),
            "iat"  => $time,
            "nbf"  => $time,
            "exp"  => strtotime("+".env("jwt.time.exp.refresh")." minutes", $time),
            "type" => "refresh",
            "user" => (int) $userId
        ];
        $refresh = JWT::encode($payload_b, env("jwt.secret.key.refresh"));
        return [
            "access"  => $access,
            "refresh" => $refresh
        ];
    }
}