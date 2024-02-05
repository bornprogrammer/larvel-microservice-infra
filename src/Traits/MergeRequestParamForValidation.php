<?php

namespace Laravel\Infrastructure\Traits;

trait MergeRequestParamForValidation
{
    public function validationData()
    {
        return $this->route()->parameters() + $this->all();
    }
}
