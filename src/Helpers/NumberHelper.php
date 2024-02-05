<?php

namespace Laravel\Infrastructure\Helpers;

class NumberHelper
{
  public static function setNumberFormat($number, $delim = 2): ?string
  {
    if (is_numeric($number))
      return strval(number_format($number, $delim));
    return $number;
  }
}
