<?php

/**
 *    Copyright (C) 2018 - CounterFlow AI, Inc
 *    Copyright (C) 2018 Max Orelus
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

namespace Elastic\Filebeat\Api;

use \Elastic\Filebeat\Filebeat;
use \OPNsense\Base\ApiControllerBase;
use \OPNsense\Core\Config;

class SettingsController extends ApiControllerBase
{

    /**
     * retrieve Filebeat settings
     * @return array settings
     */
    public function getAction()
    {
        $this->sessionClose();

        $data = array();

        if ($this->request->isGet()) {
            $model = new Filebeat();
            $data['filebeat'] = $model->getNodes();
        }
        return $data;
    }

    /**
     * update Filebeat settings
     * @return array settings
     */
    public function setAction()
    {
        $this->sessionClose();

        $data = array('results' => 'failed');

        if ($this->request->isPost()) {
            // load model and update with provided data
            $model = new Filebeat();

            $model->setNodes($this->request->getPost('filebeat'));
            // OPNids basic validation
            $validation = $model->performValidation();
            foreach ($validation as $field => $msg) {
                if (!array_key_exists('validations', $data)) {
                    $data['validations'] = array();
                }
                $data['validations']['filebeat.' . $msg->getField()] = $msg->getMessage();
            }

            // @TODO: Add validation to free form textbox areas (hosts, paths)

            // custom validation
            // $hosts = explode("\n", $model->outputs->elasticsearch->hosts->__toString());
            // foreach ($hosts as $host) {
            //     $isValidHost = (is_ipaddr($host) || is_ipaddrwithport($host) || is_hostname($host) || is_hostnamewithport($host));

            //     if (!$isValidHost) {
            //         $data['validations']['filebeat.outputs.elasticsearch.hosts'] = $host . '1 or more host(s) are invalid';
            //     }
            // }

            // serialize model to config and save
            if ($validation->count() == 0) {
                $model->serializeToConfig();
                Config::getInstance()->save();
                $data['results'] = 'saved';
            }
        }
        return $data;
    }
}
