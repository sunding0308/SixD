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

namespace Ram\Request\V20150501;

class DeletePublicKeyRequest extends \RpcAcsRequest
{
    public function __construct()
    {
        parent::__construct('Ram', '2015-05-01', 'DeletePublicKey');
        $this->setProtocol('https');
        $this->setMethod('POST');
    }

    private $userPublicKeyId;

    private $userName;

    public function getUserPublicKeyId()
    {
        return $this->userPublicKeyId;
    }

    public function setUserPublicKeyId($userPublicKeyId)
    {
        $this->userPublicKeyId = $userPublicKeyId;
        $this->queryParameters['UserPublicKeyId'] = $userPublicKeyId;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
        $this->queryParameters['UserName'] = $userName;
    }
}
