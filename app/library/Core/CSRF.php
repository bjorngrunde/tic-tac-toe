<?php

namespace App\Core;

final class CSRF
{
  private string $token;

  public function generateToken(): CSRF
  {
    $this->token = bin2hex(random_bytes(35));
    return $this;
  }

  public function putToken(): string
  {
    return $_SESSION['csrf_token'];
  }

  public function setCSRFToken(): CSRF
  {
    if (!isset($_SESSION['csrf_token'])) {
      $this->generateToken();
      $_SESSION['csrf_token'] = $this->token;
    }
    return $this;
  }

  public function forceFullySetNewToken(): void
  {
    $_SESSION['csrf_token'] = $this->token;
  }

  public function handleCSRFToken(string $token): bool
  {
    return $_SESSION['csrf_token'] !== $token;
  }
}
