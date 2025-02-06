<?php

namespace App\Core;

final class CSRF
{

  public static function setCSRFToken(): string
  {
    $token = bin2hex(random_bytes(35));
    $_SESSION['csrf_token'] = $token;

    return $token;
  }

  public static function handleCSRFToken(string $token): bool
  {
    return $_SESSION['csrf_token'] === $token;
  }
}
