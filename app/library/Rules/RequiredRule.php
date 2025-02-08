<?php

namespace App\Rules;

use App\Rules\RuleInterface;

class RequiredRule implements RuleInterface
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
