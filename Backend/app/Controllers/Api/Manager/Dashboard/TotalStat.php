<?php namespace App\Controllers\Api\Manager\Dashboard;

use App\Controllers\PrivateController;
use App\Libraries\Settings;
use App\Models\AppsModel;
use App\Models\BuildsModel;
use App\Models\DepositMethodsModel;
use App\Models\PlansModel;
use App\Models\UsersModel;
use CodeIgniter\HTTP\ResponseInterface;

class TotalStat extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get dashboard total and warning data
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $users = new UsersModel();
        $projects = new AppsModel();
        $settings = new Settings();

        $paid_apps_total = $projects
            ->where("status", 1)
            ->countAllResults();

        $builds = new BuildsModel();

        $notifications = [];

        if (!$settings->get_config("license")) {
            $notifications[] = lang("Message.message_91");
        }

        if (!$settings->get_config("codemagic_id") || !$settings->get_config("codemagic_key")) {
            $notifications[] = lang("Message.message_97");
        }

        if (!$settings->get_config("one_signal_fcm_file")) {
            $notifications[] = lang("Message.message_92");
        }

        $plans = new PlansModel();

        $plans_total = $plans
            ->where("deleted_at", 0)
            ->countAllResults();

        if (!$plans_total) {
            $notifications[] = lang("Message.message_96");
        }

        $providers = new DepositMethodsModel();

        $providers_total = $providers
            ->where("status", 1)
            ->countAllResults();

        if (!$providers_total) {
            $notifications[] = lang("Message.message_95");
        }

        return $this->respond([
            "users"         => $users->countAllResults(),
            "apps"          => $projects->countAllResults(),
            "paid_apps"     => $paid_apps_total,
            "builds"        => $builds->countAllResults(),
            "pending_apps"  => $this->get_pending_apps(),
            "notifications" => $notifications
        ], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get pending onesignal settings apps
     * @return array
     */
    private function get_pending_apps(): array
    {
        $projects = new AppsModel();

        return $projects
            ->where("status", 1)
            ->where("one_signal_id !=", "")
            ->where("one_signal_rest", "")
            ->select("name,uid")
            ->orderBy("id", "ASC")
            ->findAll();
    }
}