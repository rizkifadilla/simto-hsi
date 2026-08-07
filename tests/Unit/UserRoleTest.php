<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_admin_role()
    {
        $user = new User([
            'role' => 'admin'
        ]);

        $this->assertTrue(
            $user->isAdmin()
        );
    }

    public function test_employee_role()
    {
        $user = new User([
            'role' => 'employee'
        ]);

        $this->assertTrue(
            $user->isEmployee()
        );
    }
}