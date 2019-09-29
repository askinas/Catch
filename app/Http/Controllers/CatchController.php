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
    public function processOrder()
    {
        echo "ini test\n";

        $source = 'https://s3-ap-southeast-2.amazonaws.com/catch-code-challenge/challenge-1-in.jsonl';
        $filename = $this->_downloadFile($source);

        $this->_processFile($filename);
        
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
    
    private function _processFile($filename)
    {
        $file = fopen($filename,"r");
  
        $i = 0;
        while(! feof($file))  {
          $result = fgets($file);
          echo $i++ . ". ". $result."\n";
        }
      
        fclose($file);
    }
}
