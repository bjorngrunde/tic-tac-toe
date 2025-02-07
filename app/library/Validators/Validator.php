<?php

namespace App\Core\Validators;

use App\Core\Rules\RuleInterface;

class Validator
{
  private array $rules = [];
  private array $errors = [];

  public function add(string $alias, RuleInterface $rule): void
  {
    $this->rules[$alias] = $rule;
  }

  public function validate(array $formData, array $fields): void
  {

    foreach ($fields as $fieldName => $rules) {
      foreach ($rules as $rule) {
        $ruleParams = [];

        if (str_contains($rule, ':')) {
          [$rule, $ruleParams] = explode(':', $rule);
          $ruleParams = explode(',', $ruleParams);
        }

        $ruleValidator = $this->rules[$rule];

        if ($ruleValidator->validate($formData, $fieldName, $ruleParams)) {
          continue;
        }

        $this->errors[$fieldName][] = $ruleValidator->getMessage($formData, $fieldName, $ruleParams);
      }
    }
  }

  public function hasErrors(): bool
  {
    return count($this->errors) > 0;
  }

  public function getErrors(): array
  {
    return $this->errors;
  }
}
