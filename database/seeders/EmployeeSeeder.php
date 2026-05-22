<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        if (! DB::table('roles')->where('description', 'faculty')->exists()) {
            $this->call(RoleSeeder::class);
        }

        $facultyRoleId = DB::table('roles')->where('description', 'faculty')->value('id');

        $employees = [
            [
                'employee_id' => 'EMP-2024-001',
                'employee_number' => 'EN-1001',
                'firstname' => 'Rosa',
                'lastname' => 'Mendoza',
                'department' => 'Library Services',
                'position' => 'Head Librarian',
                'birth_date' => '1985-04-12',
                'sex' => 'Female',
            ],
            [
                'employee_id' => 'EMP-2024-002',
                'employee_number' => 'EN-1002',
                'firstname' => 'Carlos',
                'lastname' => 'Villanueva',
                'department' => 'Information Technology',
                'position' => 'Systems Administrator',
                'birth_date' => '1990-08-03',
                'sex' => 'Male',
            ],
            [
                'employee_id' => 'EMP-2024-003',
                'employee_number' => 'EN-1003',
                'firstname' => 'Elena',
                'lastname' => 'Torres',
                'department' => 'Registrar',
                'position' => 'Records Officer',
                'birth_date' => '1988-12-20',
                'sex' => 'Female',
            ],
            [
                'employee_id' => 'EMP-2024-004',
                'employee_number' => 'EN-1004',
                'firstname' => 'Miguel',
                'lastname' => 'Fernandez',
                'department' => 'Student Affairs',
                'position' => 'Coordinator',
                'birth_date' => '1992-06-15',
                'sex' => 'Male',
            ],
            [
                'employee_id' => 'EMP-2024-005',
                'employee_number' => 'EN-1005',
                'firstname' => 'Grace',
                'lastname' => 'Bautista',
                'department' => 'Library Services',
                'position' => 'Circulation Staff',
                'birth_date' => '1995-02-28',
                'sex' => 'Female',
            ],
        ];

        foreach ($employees as $row) {
            $row['role_id'] = $facultyRoleId;
            $row['qrcode'] = 'E-'.$row['employee_id'];
            $row['civil_status'] = $row['civil_status'] ?? 'Single';

            Employee::updateOrCreate(
                ['employee_id' => $row['employee_id']],
                $row
            );
        }
    }
}
