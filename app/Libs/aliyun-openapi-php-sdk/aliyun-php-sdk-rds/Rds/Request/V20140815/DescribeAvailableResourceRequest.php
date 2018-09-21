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

namespace Rds\Request\V20140815;

class DescribeAvailableResourceRequest extends \RpcAcsRequest
{
    public function __construct()
    {
        parent::__construct('Rds', '2014-08-15', 'DescribeAvailableResource', 'rds', 'openAPI');
        $this->setMethod('POST');
    }

    private $resourceOwnerId;

    private $resourceOwnerAccount;

    private $ownerAccount;

    private $engineVersion;

    private $ownerId;

    private $engine;

    private $zoneId;

    private $dBInstanceId;

    private $instanceChargeType;

    private $orderType;

    public function getResourceOwnerId()
    {
        return $this->resourceOwnerId;
    }

    public function setResourceOwnerId($resourceOwnerId)
    {
        $this->resourceOwnerId = $resourceOwnerId;
        $this->queryParameters['ResourceOwnerId'] = $resourceOwnerId;
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

    public function getOwnerAccount()
    {
        return $this->ownerAccount;
    }

    public function setOwnerAccount($ownerAccount)
    {
        $this->ownerAccount = $ownerAccount;
        $this->queryParameters['OwnerAccount'] = $ownerAccount;
    }

    public function getEngineVersion()
    {
        return $this->engineVersion;
    }

    public function setEngineVersion($engineVersion)
    {
        $this->engineVersion = $engineVersion;
        $this->queryParameters['EngineVersion'] = $engineVersion;
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

    public function getEngine()
    {
        return $this->engine;
    }

    public function setEngine($engine)
    {
        $this->engine = $engine;
        $this->queryParameters['Engine'] = $engine;
    }

    public function getZoneId()
    {
        return $this->zoneId;
    }

    public function setZoneId($zoneId)
    {
        $this->zoneId = $zoneId;
        $this->queryParameters['ZoneId'] = $zoneId;
    }

    public function getDBInstanceId()
    {
        return $this->dBInstanceId;
    }

    public function setDBInstanceId($dBInstanceId)
    {
        $this->dBInstanceId = $dBInstanceId;
        $this->queryParameters['DBInstanceId'] = $dBInstanceId;
    }

    public function getInstanceChargeType()
    {
        return $this->instanceChargeType;
    }

    public function setInstanceChargeType($instanceChargeType)
    {
        $this->instanceChargeType = $instanceChargeType;
        $this->queryParameters['InstanceChargeType'] = $instanceChargeType;
    }

    public function getOrderType()
    {
        return $this->orderType;
    }

    public function setOrderType($orderType)
    {
        $this->orderType = $orderType;
        $this->queryParameters['OrderType'] = $orderType;
    }
}