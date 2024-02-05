<?php

namespace Laravel\Infrastructure\Constants;

class SearchFilterOperatorTypeConstant
{
  const OPERATOR_TYPE_EQUAL = "equal";

  const OPERATOR_TYPE_LIKE = "like";

  const OPERATOR_TYPE_BETWEEN = "between";

  const OPERATOR_TYPE_WHEREIN = "whereIn";
  const OPERATOR_TYPE_GREATER_THAN = "gt";
  const OPERATOR_TYPE_GREATER_THAN_EQUALTO = "gte";
  const OPERATOR_TYPE_LESS_THAN = "lt";

  const OPERATOR_TYPE_LESS_THAN_EQUAL_TO = "lte";
}
