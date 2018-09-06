<?php
/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

namespace pvtz\Request\V20180101;

class SetZoneRecordStatusRequest extends \RpcAcsRequest
{
    public function __construct()
    {
        parent::__construct('pvtz', '2018-01-01', 'SetZoneRecordStatus', 'pvtz', 'openAPI');
        $this->setMethod('POST');
    }

    private $recordId;

    private $userClientIp;

    private $lang;

    private $status;

    public function getRecordId()
    {
        return $this->recordId;
    }

    public function setRecordId($recordId)
    {
        $this->recordId = $recordId;
        $this->queryParameters['RecordId'] = $recordId;
    }

    public function getUserClientIp()
    {
        return $this->userClientIp;
    }

    public function setUserClientIp($userClientIp)
    {
        $this->userClientIp = $userClientIp;
        $this->queryParameters['UserClientIp'] = $userClientIp;
    }

    public function getLang()
    {
        return $this->lang;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
        $this->queryParameters['Lang'] = $lang;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->queryParameters['Status'] = $status;
    }
}
