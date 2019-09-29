<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class CatchController extends Controller
{
    /**
     * Processing Order
     * 
     * @return void 
     */
    public function processOrder($output, $type)
    {
        if ($type != "csv") {
            echo "for now only support csv as output file type.\n";
            return;
        }

        $source = 'https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl';
        $filename = $this->_downloadFile($source);

        $this->_processFile($filename, $output, $type);

        echo "\n";
       
    }

    private function _downloadFile($source)
    {
        $client = new Client(
            [
            'stream' => true, 
            'debug'=>true
            ]
        );

        $response = $client->get($source);
        $body = $response->getBody();

        $filename = "catch" . time() .".tmp";
        while (!$body->eof()) {
            file_put_contents($filename, $body->read(10000), FILE_APPEND | LOCK_EX);
        }

        return $filename;
    }
    
    private function _processFile(
        $source, 
        $destination="catch", 
        $type="csv"
    ) {
        $file = fopen($source, "r");
        $destination .= ".".$type;

        $header = [
            "order_id", 
            "order_datetime", 
            "total_order_value", 
            "average_unit_price",
            "distinct_unit_count",
            "total_units_count",
            "customer_state"
        ];
        $destination = fopen($destination, 'w');
        fputcsv($destination, $header);

        while (! feof($file)) {
            $data = fgets($file);
            $order = json_decode($data, true);

            if (json_last_error() === JSON_ERROR_NONE) {

                $total_order_value = 0;
                $total_unit_count = 0;
                $distinct_unit_count = count($order['items']);
                $customer_state = $order['customer']['shipping_address']['state'];

                foreach ($order['items'] as $item) {
                    $total_unit_count += $item['quantity'];
                    $total_order_value += $item['quantity'] * $item['unit_price'];
                }

                $average_unit_price = $total_order_value / $total_unit_count;
                $total_order_value += $order['shipping_price'];

                $order_discount = 0;
                if (count($order['discounts']) > 0) {
                    foreach ($order['discounts'] as $discount) {
                        $order_discount += $discount['value'];
                    }
                }
                $total_order_value += $order_discount;

                if ($total_order_value > 0) {
                    $record = [
                        $order['order_id'],
                        $order['order_date'],
                        $total_order_value, 
                        $average_unit_price,
                        $distinct_unit_count,
                        $total_unit_count,
                        $customer_state
                    ];
                    fputcsv($destination, $record);
                }
            }
        }

        fclose($destination);
        fclose($file);
    }
}
