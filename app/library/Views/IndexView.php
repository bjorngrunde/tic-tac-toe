<?php

namespace App\Views;

class IndexView extends AbstractView
{
    public int $gridSize;

    public function __construct()
    {
        $this->setTitle("Lets play Tic-Tac-Toe!");
    }

    public function render(): void
    {
        include __DIR__ . '/index.phtml';
    }

    public function setTableClass(int $row, int $col, int $gridSize): string
    {
        if (($row === 1 || $row === $gridSize) && ($col === 1 || $col === $gridSize)) {
            return '';
        }
        if ($col === 1 || $col === $gridSize) {
            return 'hori';
        }

        if ($row === 1 || $row === $gridSize) {
            return 'vert';
        }

        return 'hori vert';
    }
}
