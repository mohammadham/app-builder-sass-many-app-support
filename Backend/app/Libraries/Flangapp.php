<?php namespace App\Libraries;

use Config\Services;

class Flangapp
{
    private ?string $licenseKey;

    /**
     * Create models, config and library's
     */
    function __construct()
    {
        $settings = new Settings();
        $this->licenseKey = $settings->get_config("license");
    }

    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * License activation (Bypass external API call)
     * @param string $code
     * @return array
     */
    public function activation_license(string $code): array
    {
        // Bypass external API, just validate locally if needed
        if ($code === 'Megaeducate@555') {
            return ["event" => true];
        }

        return [
            "event" => false,
            "message" => "Invalid license code."
        ];
    }

    /**
     * Get snack project preview files (bypass API call)
     * @return array
     */
    public function get_snacks(): array
    {
        // Mock data or return empty as bypass
        return [
            "event" => true,
            "data" => [] // Replace with actual local data if needed
        ];
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

        // Mock the process of creating a keystore file
        $name = random_string("alpha", 10) . ".jks";
        $filePath = WRITEPATH . 'storage/android/' . $name;

        // You can generate a mock content or simply create a blank file
        $content = "Mock keystore content for alias: $alias";
        file_put_contents($filePath, $content);

        return ["event" => true, "name" => $name];
    }

    /**
     * Create .pem file for ios signature (bypass API call)
     * @return array
     */
    public function create_pem(): array
    {
        helper('text');

        // Mock the process of creating a PEM file
        $name = random_string("alpha", 10) . ".pem";
        $filePath = WRITEPATH . 'storage/pub/' . $name;

        // Generate mock content or leave it blank
        $content = "Mock PEM content";
        file_put_contents($filePath, $content);

        return ["event" => true, "name" => $name];
    }
}
