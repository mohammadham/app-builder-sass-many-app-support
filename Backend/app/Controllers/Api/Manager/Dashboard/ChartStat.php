<?php namespace App\Controllers\Api\Manager\Dashboard;

use App\Controllers\PrivateController;
use App\Models\BuildsModel;
use App\Models\TransactionsModel;
use CodeIgniter\HTTP\ResponseInterface;

class ChartStat extends PrivateController
{
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/

    /**
     * Get chart
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $period = esc($this->request->getGet("period")); // last_month or last_year

        if ($period == "last_month") {
            $min_date_year = strtotime(date('d-m-Y',strtotime('first day of this month')));
            $max_date_year = strtotime(date('d-m-Y',strtotime('last day of this month')));
        } else {
            $min_date_year = strtotime(date('d-m-Y',strtotime('first day of this year')));
            $max_date_year = strtotime(date('d-m-Y',strtotime('last day of this year')));
        }

        $transactions = new TransactionsModel();

        $list = $transactions
            ->where('created_at between '.$min_date_year.' and '.$max_date_year)
            ->findAll();

        $count = $transactions
            ->where('created_at between '.$min_date_year.' and '.$max_date_year)
            ->countAllResults();

        $builds = new BuildsModel();

        $builds_total = $builds
            ->where('created_at between '.$min_date_year.' and '.$max_date_year)
            ->countAllResults();

        $amount = 0;
        $datasets = [];
        $labels = [];

        if ($period == "last_month") {
            for ($i = 1; $i <= date('t'); $i ++) {
                $labels[] = $i;
                $datasets[$i] = 0;
                foreach ($list as $item) {
                    $day = date('d', $item["created_at"]);
                    if ($day == $i) {
                        $datasets[$i] = $datasets[$i] + $item["amount"];
                    }
                }
                $amount = $amount + $datasets[$i];
            }
        } else {
            for ($i = 1; $i <= 12; $i ++) {
                $key = $this->get_month($i);
                $labels[] = $key;
                $datasets[$key] = 0;
                foreach ($list as $item) {
                    $month = date('m', $item["created_at"]);
                    if ($month == $i) {
                        $datasets[$key] = $datasets[$key] + $item["amount"];
                    }
                }
                $amount = $amount + $datasets[$key];
            }
        }

        return $this->respond([
            "labels"   => $labels,
            "data"     => $datasets,
            "total"    => $count,
            "amount"   => $amount,
            "builds"   => $builds_total
        ], 200);
    }

    /**************************************************************************************
     * PRIVATE FUNCTIONS
     **************************************************************************************/

    /**
     * Get month name
     * @param int $number
     * @return string
     */
    private function get_month(int $number): string
    {
        switch ($number) {
            case 1:
                return lang("Fields.field_108");
            case 2:
                return lang("Fields.field_109");
            case 3:
                return lang("Fields.field_110");
            case 4:
                return lang("Fields.field_111");
            case 5:
                return lang("Fields.field_112");
            case 6:
                return lang("Fields.field_113");
            case 7:
                return lang("Fields.field_114");
            case 8:
                return lang("Fields.field_115");
            case 9:
                return lang("Fields.field_116");
            case 10:
                return lang("Fields.field_117");
            case 11:
                return lang("Fields.field_118");
            case 12:
                return lang("Fields.field_119");
            default:
                return "-";
        }
    }

}