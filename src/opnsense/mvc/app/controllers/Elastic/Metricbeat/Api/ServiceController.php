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
namespace Elastic\Metricbeat\Api;

use \Elastic\Metricbeat\Metricbeat;
use \OPNsense\Base\ApiControllerBase;
use \OPNsense\Core\Backend;

/**
 * Class ServiceController
 * @package Elastic\Metricbeat
 */
class ServiceController extends ApiControllerBase
{
    /**
     * start Metricbeat service (in background)
     * @return array
     */
    public function startAction()
    {
        $this->sessionClose();

        if ($this->request->isPost()) {
            $backend = new Backend();
            $response = trim($backend->configdRun('metricbeat start'));
            return array("response" => $response);
        } else {
            return array("response" => array());
        }
    }

    /**
     * stop metricbeat service
     * @return array
     */
    public function stopAction()
    {
        $this->sessionClose();

        if ($this->request->isPost()) {
            $backend = new Backend();
            $response = trim($backend->configdRun("metricbeat stop"));
            return array("response" => $response);
        } else {
            return array("response" => array());
        }
    }

    /**
     * restart metricbeat service
     * @return array
     */
    public function restartAction()
    {
        $this->sessionClose();

        if ($this->request->isPost()) {
            $backend = new Backend();
            $response = trim($backend->configdRun("metricbeat restart"));
            return array("response" => $response);
        } else {
            return array("response" => array());
        }
    }

    /**
     * retrieve status of Metricbeat
     * @return array
     * @throws \Exception
     */
    public function statusAction()
    {
        $this->sessionClose();

        $status = "unknown";
        $backend = new Backend();
        $model = new Metricbeat();
        $response = trim($backend->configdRun("metricbeat status"));

        return array("status" => $response);
    }

    /**
     * test configuration file
     */
    public function testConfigAction()
    {
        $this->sessionClose();
        $status = "Configuration test failed. Please check your configuration";

        if ($this->request->isPost()) {
            $backend = new Backend();
            $response = trim($backend->configdRun('metricbeat test config'));
            if ($response) {
                $status = $response;
            }
        }

        return array("status" => $status);
    }

    /**
     * test output connection
     */
    public function testConnectionAction()
    {
        $this->sessionClose();
        $status = "Connection test failed. Please check your configuration";

        if ($this->request->isPost()) {
            $backend = new Backend();
            $response = trim($backend->configdRun('metricbeat test connection'));
            if ($response) {
                $status = "Connection OK";
            }
        }

        return array("status" => $status);
    }

    /**
     * reconfigure Metricbeat
     */
    public function reloadAction()
    {
        $this->sessionClose();

        $status = "failed";
        if ($this->request->isPost()) {
            $backend = new Backend();
            $bckresult = trim($backend->configdRun('template reload Elastic/Metricbeat'));
            if ($bckresult == "OK") {
                $status = "ok";
            }
        }
        return array("status" => $status);
    }
}
