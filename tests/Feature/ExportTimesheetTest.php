<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExportTimesheetTest extends TestCase
{
    public function test_employee_can_export_timesheet()
    {
        $data = $this->createEmployeeUser();

        $response = $this->actingAs(
            $data['user']
        )->get('/my-attendance/export?from=2026-01-01&to=2026-01-31');

        $response->assertStatus(200);

        $response->assertHeader(
            'Content-Type',
            'application/vnd.ms-excel'
        );
    }
}