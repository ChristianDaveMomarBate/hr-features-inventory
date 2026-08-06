<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class employeeData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:transfer-employee-records';

    /**
     * The console command description.
     */
    protected $description = 'Transfer employee data from hrmps.employee to hr_system.employeeData';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting transfer from hrmps.employees to hrmis_confidentiaData.employeeData...');

        $records = DB::table('hrmps.employees')
            ->get();

        $this->info('Transferring '.$records->count().' records...');
        $this->output->progressStart($records->count());

        foreach ($records as $record) {
            DB::table('hrmis_confidentialData.employeeData')->insert([
                'empID' => $record->empID,
                'lname' => $record->lname,
                'fname' => $record->fname,
                'mname' => $record->mname,
                'gender' => $record->gender,
                'status' => $record->status,
                'currentAdd' => $record->currentAdd,
                'permanentAdd' => $record->permanentAdd,
                'mobile' => $record->mobile,
                'positionID' => $record->positionID,
                'plantillaNo' => $record->plantillaNo,
                'empStatusID' => $record->empStatusID,
                'dateEmployed' => $record->dateEmployed,
                'last_appointment' => $record->last_appointment,
                'officeID' => $record->officeID,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('Transfer complete!');
    }
}
