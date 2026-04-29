<?php

namespace App\Console\Commands;

use App\Models\Patrol;
use App\Models\Employee;
use Illuminate\Console\Command;

class DebugChecksheetGroup extends Command
{
    protected $signature = 'debug:checksheet-group';
    protected $description = 'Debug missing groups in checksheet patrol';

    public function handle(): void
    {
        $this->info('🔍 Debugging Checksheet Patrol Groups...\n');

        // Check patrols
        $totalPatrols = Patrol::count();
        $patrolsWithEmployee = Patrol::whereNotNull('employee_id')->count();
        $patrolsWithoutEmployee = Patrol::whereNull('employee_id')->count();

        $this->info("📊 Patrol Statistics:");
        $this->info("  Total patrols: {$totalPatrols}");
        $this->info("  With employee_id: {$patrolsWithEmployee}");
        $this->info("  Without employee_id (NULL): {$patrolsWithoutEmployee}\n");

        // Check employees
        $totalEmployees = Employee::count();
        $employeesWithGroup = Employee::whereNotNull('shfgroup')->count();
        $employeesWithoutGroup = Employee::whereNull('shfgroup')->count();

        $this->info("👥 Employee Statistics:");
        $this->info("  Total employees: {$totalEmployees}");
        $this->info("  With shfgroup: {$employeesWithGroup}");
        $this->info("  Without shfgroup (NULL): {$employeesWithoutGroup}\n");

        // Show sample patrols with their employees
        $this->info("📝 Recent Patrols with Employees:\n");
        
        $patrols = Patrol::with('employee')
            ->orderBy('patrol_time', 'desc')
            ->limit(5)
            ->get();

        foreach ($patrols as $i => $patrol) {
            $empId = $patrol->employee_id ?? 'NULL';
            $empName = $patrol->employee?->name ?? '-';
            $empGroup = $patrol->employee?->shfgroup ?? 'NULL/EMPTY';
            
            $this->info("{$i}. Patrol #{$patrol->id}");
            $this->info("   Employee ID: {$empId}");
            $this->info("   Employee Name: {$empName}");
            $this->info("   Employee Group (shfgroup): {$empGroup}");
            $this->line('');
        }

        // If patrols missing employee_id
        if ($patrolsWithoutEmployee > 0) {
            $this->warn("\n⚠️  WARNING: {$patrolsWithoutEmployee} patrols have NO employee_id!");
            $this->info("   → Patrols harus punya employee_id agar bisa tampil group\n");
        }

        // If employees missing group
        if ($employeesWithoutGroup > 0) {
            $this->warn("\n⚠️  WARNING: {$employeesWithoutGroup} employees have NO shfgroup!");
            $this->info("   → Update employees dengan shfgroup yang sesuai\n");
        }

        if ($patrolsWithoutEmployee === 0 && $employeesWithoutGroup === 0) {
            $this->info("\n✅ Semua data OK! Group seharusnya muncul di checksheet.\n");
        }
    }
}
