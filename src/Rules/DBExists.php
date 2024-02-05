<?php

namespace Laravel\Infrastructure\Rules;

use Laravel\Infrastructure\Rules\BaseRule;
use Illuminate\Validation\Rule;
use Laravel\Infrastructure\Constants\EntityStatusConstant;

class DBExists extends BaseRule
{
  protected bool $isRequired;
  public function __construct(protected string $tableName, protected ?string $column = 'NULL')
  {
    $this->isRequired = true;
  }

  public function passes($attribute, $value): bool
  {
    $self = $this;
    $rules = [];
    if ($this->isRequired) {
      $rules = ["required"];
    }
    $rules[] = Rule::exists($this->tableName, $this->column)->where(function ($query) use ($self, $value) {
      $self->whereBuilder($query, $value);
    });
    return $this->validate($value, $rules, $attribute);
  }

  protected function whereBuilder($query, $value)
  {
    $this->statusWhereBuilder($query, $value);
    $this->extraWhereBuilder($query, $value);
  }

  protected function statusWhereBuilder($query, $value)
  {
    $query->where("status", EntityStatusConstant::ACTIVE);
  }

  protected function extraWhereBuilder($query, $value)
  {
    return $query;
  }
}
