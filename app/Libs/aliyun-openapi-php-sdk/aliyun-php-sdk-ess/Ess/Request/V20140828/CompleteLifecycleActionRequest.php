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

namespace Ess\Request\V20140828;

class CompleteLifecycleActionRequest extends \RpcAcsRequest
{
    public function __construct()
    {
        parent::__construct('Ess', '2014-08-28', 'CompleteLifecycleAction', 'ess', 'openAPI');
        $this->setMethod('POST');
    }

    private $lifecycleActionToken;

    private $resourceOwnerAccount;

    private $lifecycleHookId;

    private $ownerAccount;

    private $ownerId;

    private $lifecycleActionResult;

    public function getLifecycleActionToken()
    {
        return $this->lifecycleActionToken;
    }

    public function setLifecycleActionToken($lifecycleActionToken)
    {
        $this->lifecycleActionToken = $lifecycleActionToken;
        $this->queryParameters['LifecycleActionToken'] = $lifecycleActionToken;
    }

    public function getResourceOwnerAccount()
    {
        return $this->resourceOwnerAccount;
    }

    public function setResourceOwnerAccount($resourceOwnerAccount)
    {
        $this->resourceOwnerAccount = $resourceOwnerAccount;
        $this->queryParameters['ResourceOwnerAccount'] = $resourceOwnerAccount;
    }

    public function getLifecycleHookId()
    {
        return $this->lifecycleHookId;
    }

    public function setLifecycleHookId($lifecycleHookId)
    {
        $this->lifecycleHookId = $lifecycleHookId;
        $this->queryParameters['LifecycleHookId'] = $lifecycleHookId;
    }

    public function getOwnerAccount()
    {
        return $this->ownerAccount;
    }

    public function setOwnerAccount($ownerAccount)
    {
        $this->ownerAccount = $ownerAccount;
        $this->queryParameters['OwnerAccount'] = $ownerAccount;
    }

    public function getOwnerId()
    {
        return $this->ownerId;
    }

    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        $this->queryParameters['OwnerId'] = $ownerId;
    }

    public function getLifecycleActionResult()
    {
        return $this->lifecycleActionResult;
    }

    public function setLifecycleActionResult($lifecycleActionResult)
    {
        $this->lifecycleActionResult = $lifecycleActionResult;
        $this->queryParameters['LifecycleActionResult'] = $lifecycleActionResult;
    }
}
