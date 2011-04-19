<?php
require_once 'HTTP/Request2.php';

class Lagged_Munin extends HTTP_Request2
{
    protected $debug  = false;
    protected $errors = array();

    public function setDebug($flag)
    {
        if (!is_bool($flag)) {
            throw new InvalidArgumentException("Must be a boolean.");
        }
        $this->debug = $flag;
        return $this;
    }

    public function getTotals($dbs)
    {
        $this->setConfig(array('timeout' => 2,));
        $this->setMethod(HTTP_Request2::METHOD_GET);

        $total = 0;
        foreach ($dbs as $db) {
            try {
                $response = $this->setUrl($db)->send();
                switch ($response->getStatus()) {
                case '403':
                    //return $this->handleError(new UnexpectedValueException("Setup problem: {$db}"));
                    continue;
                default:
                    //return $this->handleError(new UnexpectedValueException("Problem?"));
                    continue;
                case '200':
                    break;
                }
                $metaData = json_decode($response->getBody());
                if (isset($metaData->doc_count)) {
                    $total += intval($metaData->doc_count);
                }
            } catch (Exception $e) {
                //return $this->handleError($e);
                continue;
            }
            sleep(1);
        }
        return $total;
    }

    protected function handleError(Exception $e)
    {
        if ($this->debug === true) {
            throw $e;
        }
        // log it
        return 'U';
    }
}
