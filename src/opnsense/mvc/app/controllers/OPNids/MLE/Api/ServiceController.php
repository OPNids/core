<?php
/**
 *    Copyright (C) 2018 CounterFlow AI, Inc
 *
 *    All rights reserved.
 *
 *    Redistribution and use in source and binary forms, with or without
 *    modification, are permitted provided that the following conditions are met:
 *
 *    1. Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *
 *    2. Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 *    THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
 *    INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
 *    AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 *    AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 *    OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 *    SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 *    INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 *    CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 *    ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 *    POSSIBILITY OF SUCH DAMAGE.
 *
 */
namespace OPNids\MLE\Api;

use \OPNids\MLE\MLE;
use \OPNsense\Base\ApiControllerBase;
use \OPNsense\Core\Backend;
use \OPNsense\Core\Router;

/**
 * Class ServiceController
 * @package OPNids\MLE
 */
class ServiceController extends ApiControllerBase
{
    /**
     * start MLE service (in background)
     * @return array
     */
    public function startAction()
    {
        $this->sessionClose();

        if ($this->request->isPost()) {
            $backend = new Backend();
            $response = trim($backend->configdRun('dragonflymle start'));
            return array("response" => $response);
        } else {
            return array("response" => array());
        }
    }

    /**
     * stop mle service
     * @return array
     */
    public function stopAction()
    {
        $this->sessionClose();

        if ($this->request->isPost()) {
            $backend = new Backend();
            $response = trim($backend->configdRun("dragonflymle stop"));
            return array("response" => $response);
        } else {
            return array("response" => array());
        }
    }

    /**
     * restart mle service
     * @return array
     */
    public function restartAction()
    {
        $this->sessionClose();

        if ($this->request->isPost()) {
            $backend = new Backend();
            $response = trim($backend->configdRun("dragonflymle restart"));
            return array("response" => $response);
        } else {
            return array("response" => array());
        }
    }

    /**
     * retrieve status of mle
     * @return array
     * @throws \Exception
     */
    public function statusAction()
    {
        $this->sessionClose();

        $status = "unknown";
        $backend = new Backend();
        $model = new MLE();
        $response = trim($backend->configdRun("dragonflymle status"));

        return array("status" => $response);
    }

    /**
     * reconfigure mle
     */
    public function reloadAction()
    {
        $this->sessionClose();

        $status = "failed";
        if ($this->request->isPost()) {
            $backend = new Backend();
            $bckresult = trim($backend->configdRun('template reload OPNids/MLE'));
            if ($bckresult == "OK") {
                $status = "ok";
            }
        }
        return array("status" => $status);
    }

    public function getAnalyzerAction()
    {
        // grab $analyzer_name from url
        $params = $this->dispatcher->getParams();

        // set response header
        $this->response->setRawHeader("Content-Type: application/json");

        if (!count($params)) {
            return '{"error": "Malformed request"}';
        }

        $analyzer_name = $params[0];

        // fire off python script to fetch analyzer
        $backend = new Backend();
        $response = $backend->configdpRun("dragonflymle fetch_analyzer", array($analyzer_name));
        
        return trim($response);
    }
}
