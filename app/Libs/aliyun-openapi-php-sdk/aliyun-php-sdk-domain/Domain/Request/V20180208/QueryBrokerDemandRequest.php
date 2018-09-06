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

namespace Domain\Request\V20180208;

class QueryBrokerDemandRequest extends \RpcAcsRequest
{
    public function __construct()
    {
        parent::__construct('Domain', '2018-02-08', 'QueryBrokerDemand');
        $this->setMethod('POST');
    }

    private $pageSize;

    private $bizId;

    private $currentPage;

    private $status;

    public function getPageSize()
    {
        return $this->pageSize;
    }

    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        $this->queryParameters['PageSize'] = $pageSize;
    }

    public function getBizId()
    {
        return $this->bizId;
    }

    public function setBizId($bizId)
    {
        $this->bizId = $bizId;
        $this->queryParameters['BizId'] = $bizId;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        $this->queryParameters['CurrentPage'] = $currentPage;
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
