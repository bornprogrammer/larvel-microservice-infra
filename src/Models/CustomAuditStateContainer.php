<?php

namespace Laravel\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Log\Logger;

class CustomAuditStateContainer
{
  public function __construct()
  {
    Logger::info("constructed");
  }
  protected array $container =  [];


  public function setAuditState(\OwenIt\Auditing\Events\Auditing $data)
  {
    $this->container[] = $data;
  }

  public function getAuditState(): array
  {
    return $this->container;
  }

  public function transform(CustomAuditStateContainer $customAuditStateContainer)
  {

    // $customAuditStateContainer->container
    // ["App\Models\Users"=>["old_values"=>[],"new_value"]];
    // foreach ($customAuditStateContainer->container as $key => $value) {
    //   if($customAuditStateContainer->container typeOf(Accout))
    //   {
    //     $customAuditStateContainer->container[$key]['old_values']['new_key'] = $customAuditStateContainer->container[$key]['old_values']['key']===8 ?"active":"s"; 
    //     $customAuditStateContainer->container[$key]['new_values']['new_key'] = $customAuditStateContainer->container[$key]['old_values']['key']===8 ?"r":"s"; 
    //   }
    // }
  }
}
