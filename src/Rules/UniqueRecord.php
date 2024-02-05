<?php

namespace Laravel\Infrastructure\Rules;

use Laravel\Infrastructure\Facades\RequestSessionFacade;
use Laravel\Infrastructure\Rules\BaseRule;
use Illuminate\Validation\Rule;

class UniqueRecord extends BaseRule
{
    protected array $whereClause;
    public function __construct(protected string $tableName, protected string $column)
    {
        $this->whereClause = [];
    }

    public function passes($attribute, $value): bool
    {
        $rules = [];
        $rules[] = Rule::unique($this->tableName, $this->column)->where(function ($query) use ($value) {
            $this->extraWhereClause($query, $value);
            foreach ($this->whereClause as $key => $value) {
                $query->where($key, $value);
            }
        });
        $result = $this->validate($value, $rules, $attribute);
        return $result;
    }

    public function extraWhereClause($query, $value): self
    {
        $query->whereNull("deleted_at");
        return $this;
    }

    public function setOrgId(string $orgIdColumnName = "organization_id"): self
    {
        $this->whereClause[$orgIdColumnName] = RequestSessionFacade::getOrgId();
        return $this;
    }

    public function setUserOrgId(string $userOrgIdColumnName): self
    {
        $this->whereClause[$userOrgIdColumnName] = RequestSessionFacade::getUserOrgId();
        return $this;
    }
}
