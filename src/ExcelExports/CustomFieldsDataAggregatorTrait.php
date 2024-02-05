<?php

namespace Laravel\Infrastructure\ExcelExports;

use Illuminate\Database\Eloquent\Model;
use Laravel\Infrastructure\Helpers\ArrayHelper;

/**
 *
 */
trait CustomFieldsDataAggregatorTrait
{
  protected ?array $customFields;

  protected ?array $customFieldWithEmptyValue;

  protected ?string $customFormFieldKey;

  public static function makeWithCustomFields($data, string $customFormFieldKey = "custom_form_fields", ...$args): self
  {
    $excelExport = new static($data, $args);
    $excelExport->customFormFieldKey = $customFormFieldKey;
    $excelExport->init()->buildForCustomFields();
    return $excelExport;
  }

  protected function init(): self
  {
    $this->customFields = [];
    $this->customFieldWithEmptyValue = [];
    return $this;
  }

  protected function buildForCustomFields()
  {
    if ($this->data->isNotEmpty()) {
      $this->customFields = $this->data[0][$this->customFormFieldKey]['field_details'] ?? [];
    }
  }

  public function headings(): array
  {
    $staticHeadings = parent::headings();
    $this->setCustomFieldWithEmptyValue();
    $headingKeys = [...$staticHeadings, ...array_keys($this->customFieldWithEmptyValue)];
    return $headingKeys;
  }

  protected function setCustomFieldWithEmptyValue(): void
  {
    $this->customFieldWithEmptyValue = [];
    foreach ($this->customFields as $field) {
      $this->customFieldWithEmptyValue[$field['field_name']] = null;
    }
  }

  public function mapDataAgainstCustomFields(array|Model $item): array
  {
    $fieldValues = $item[$this->customFormFieldKey];
    $fieldNameWithValues = [];
    if (ArrayHelper::isArrayValid($fieldValues)) {
      foreach ($fieldValues['field_details'] as $field) {
        $fieldNameWithValues[$field['field_name']] = $field['field_values']['user_input'];
      }
      $customFieldWithValue = array_merge($this->customFieldWithEmptyValue, $fieldNameWithValues);
      return $customFieldWithValue;
    }
    return $this->customFieldWithEmptyValue;
  }
}
