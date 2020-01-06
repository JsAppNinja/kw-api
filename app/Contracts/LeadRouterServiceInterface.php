<?php

namespace App\Contracts;

interface LeadRouterServiceInterface {
  public function apply($data,  $filter);
}
