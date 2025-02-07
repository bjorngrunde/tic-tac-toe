<?php

namespace App\Core\Rules;

use App\Core\Rules\RuleInterface;

class RequiredRule extends RuleInterface
{
  public function validate(array $formData, string $field, array $params): bool
  {
    return !empty($formData[$field]);
  }

  public function getMessage(array $formData, string $field, array $params): string
  {
    return "This field is required.";
  }
}
