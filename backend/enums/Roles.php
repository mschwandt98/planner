<?php

namespace Planner\Enums;

enum Roles: string {
    case Admin = 'admin';
    case Employee = 'employee';
    case Trainee = 'trainee';
    case Student = 'student';
}
