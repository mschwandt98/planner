<?php

namespace Planner\Models;

use Planner\Enums\Roles;

class User {
    public readonly int $id;
    public string $email;
    public string $first_name;
    public string $last_name;
    public ?Roles $role;
    public ?int $profession_id;

    public function __construct(
        string $email,
        string $first_name,
        string $last_name,
        Roles|string $role = null,
        int $profession_id = null,
        int $id = 0
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->role = $role instanceof Roles ? $role : Roles::tryFrom($role);
        $this->profession_id = $profession_id;
    }
}
