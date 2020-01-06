<?php

namespace App\Contracts;

interface ListServiceInterface {

  public function createList($data);
  public function addAgent($data);
  public function addAgents($data);
  public function removeAgent($data);

}
