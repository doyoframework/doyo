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
namespace Sms\Request\V20160927;

class QuerySmsDetailByPageRequest extends \RpcAcsRequest
{
	function  __construct()
	{
		parent::__construct("Sms", "2016-09-27", "QuerySmsDetailByPage");
	}

	private  $ownerId;

	private  $resourceOwnerAccount;

	private  $resourceOwnerId;

	private  $queryTime;

	private  $recNum;

	private  $pageSize;

	private  $pageNo;

	public function getOwnerId() {
		return $this->ownerId;
	}

	public function setOwnerId($ownerId) {
		$this->ownerId = $ownerId;
		$this->queryParameters["OwnerId"]=$ownerId;
	}

	public function getResourceOwnerAccount() {
		return $this->resourceOwnerAccount;
	}

	public function setResourceOwnerAccount($resourceOwnerAccount) {
		$this->resourceOwnerAccount = $resourceOwnerAccount;
		$this->queryParameters["ResourceOwnerAccount"]=$resourceOwnerAccount;
	}

	public function getResourceOwnerId() {
		return $this->resourceOwnerId;
	}

	public function setResourceOwnerId($resourceOwnerId) {
		$this->resourceOwnerId = $resourceOwnerId;
		$this->queryParameters["ResourceOwnerId"]=$resourceOwnerId;
	}

	public function getQueryTime() {
		return $this->queryTime;
	}

	public function setQueryTime($queryTime) {
		$this->queryTime = $queryTime;
		$this->queryParameters["QueryTime"]=$queryTime;
	}

	public function getRecNum() {
		return $this->recNum;
	}

	public function setRecNum($recNum) {
		$this->recNum = $recNum;
		$this->queryParameters["RecNum"]=$recNum;
	}

	public function getPageSize() {
		return $this->pageSize;
	}

	public function setPageSize($pageSize) {
		$this->pageSize = $pageSize;
		$this->queryParameters["PageSize"]=$pageSize;
	}

	public function getPageNo() {
		return $this->pageNo;
	}

	public function setPageNo($pageNo) {
		$this->pageNo = $pageNo;
		$this->queryParameters["PageNo"]=$pageNo;
	}
	
}