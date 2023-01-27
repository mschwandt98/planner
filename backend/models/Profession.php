<?php

namespace Planner\Models;

class Profession {
    public readonly int $id;
    public string $title;

    public function __construct(string $title, int $id = 0) {
        $this->id = $id;
        $this->title = $title;
    }
}
