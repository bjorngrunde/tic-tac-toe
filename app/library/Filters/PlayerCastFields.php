<?php

namespace App\Filters;

/**
 * When we get form data, cast the data to only the fields we care about and prevent hackerman doing evil stuff.
 */
class PlayerCastFields implements CastableInterface
{
  private array $allowedFields = ['name', 'grid_size', 'play_time'];
  private array $castFields = [];

  public function cast(array $formData): void
  {
    foreach ($formData as $key => $value) {
      if (in_array($key, $this->allowedFields)) {
        $this->castFields[$key] = htmlspecialchars($value);
      }
    }
  }

  public function getFields(): array
  {
    return $this->castFields;
  }

  public function getField(string $field): mixed
  {
    if (array_key_exists($field, $this->castFields)) {
      return $this->castFields[$field];
    }

    return null;
  }
}
