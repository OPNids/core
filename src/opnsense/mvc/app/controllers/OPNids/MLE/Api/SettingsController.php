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

namespace OPNids\MLE\Api;

use \OPNids\MLE\MLE;
use \OPNsense\Base\ApiControllerBase;
use \OPNsense\Core\Config;

class SettingsController extends ApiControllerBase
{

    /**
     * retrieve MLE settings
     * @return array settings
     */
    public function getAction()
    {
        $this->sessionClose();

        $data = array();

        if ($this->request->isGet()) {
            $model = new MLE();
            $data['mle'] = $model->getNodes();
        }
        return $data;
    }

    /**
     * update MLE settings
     * @return array settings
     */
    public function setAction()
    {
        $this->sessionClose();

        $data = array('results' => 'failed');

        if ($this->request->isPost()) {
            // load model and update with provided data
            $model = new MLE();

            $model->setNodes($this->request->getPost('mle'));
            // OPNids basic validation
            $validation = $model->performValidation();
            foreach ($validation as $field => $msg) {
                if (!array_key_exists('validations', $data)) {
                    $data['validations'] = array();
                }
                $data['validations']['mle.' . $msg->getField()] = $msg->getMessage();
            }

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
