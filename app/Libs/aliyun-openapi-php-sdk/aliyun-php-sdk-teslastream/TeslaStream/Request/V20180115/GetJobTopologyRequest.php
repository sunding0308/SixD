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

namespace TeslaStream\Request\V20180115;

class GetJobTopologyRequest extends \RpcAcsRequest
{
    public function __construct()
    {
        parent::__construct('TeslaStream', '2018-01-15', 'GetJobTopology');
    }

    private $jobName;

    public function getJobName()
    {
        return $this->jobName;
    }

    public function setJobName($jobName)
    {
        $this->jobName = $jobName;
        $this->queryParameters['JobName'] = $jobName;
    }
}
