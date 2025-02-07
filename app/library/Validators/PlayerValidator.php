<?php

namespace App\Validators;

use App\Validators\Validator;
use App\Rules\{UsernameRule, MaxRule, MinRule, RequiredRule};

/**
 * In an application where we have more time we probably have a form object and a data model
 * that would go hand in hand with a validator like this one.
 */
class PlayerValidator
{
  private Validator $validator;

  public function __construct()
  {
    $this->validator = new Validator();

    $this->validator->add('required', new RequiredRule());
    $this->validator->add('min', new MinRule());
    $this->validator->add('max', new MaxRule());
    $this->validator->add('username', new UsernameRule());
  }

  public function validateRegister(array $formData): void
  {
    $this->validator->validate($formData, [
      'name' => ['required', 'min:3', 'max:20', 'username'],
      'grid_size' => ['required', 'min:3', 'max:10'],
      'play_time' => ['required']
    ]);
  }

  public function hasErrors(): bool
  {
    return $this->validator->hasErrors();
  }

  public function getErrors(): array
  {
    return $this->validator->getErrors();
  }
}
