<?php

namespace App\Core\Filters;

/**
 * When we get form data, cast the data to only the fields we care about and prevent hackerman doing evil stuff.
 */
class PlayerCastFields
{

  private array $fields = ['name', 'grid_size', 'play_time'];

  public function cast(array $formData): array
  {
    $castFormData = [];

    foreach ($formData as $key => $value) {
      if (in_array($key, $this->fields)) {
        $castFormData[$key] = htmlspecialchars($value);
      }
    }

    return $castFormData;
  }
}
