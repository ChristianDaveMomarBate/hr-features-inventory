<?php

namespace App\Http\Controllers;

use App\Models\employeeData;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeDataController extends Controller
{
    public function getEmployees()
    {
        $positions = DB::connection('hrmps')
            ->table('positions')
            ->select('positionID', 'designation')
            ->get()
            ->keyBy('positionID');

        $offices = DB::connection('hrmps')
            ->table('offices')
            ->select('officeID', 'officeName', 'office')
            ->get()
            ->keyBy('officeID');

        $employmentStatuses = DB::connection('hrmps')
            ->table('employment_status')
            ->select('empStatusID', 'type')
            ->get()
            ->keyBy('empStatusID');

        $employees = employeeData::select(
            'empID', 'lname', 'fname', 'mname', 'gender', 'status',
            'currentAdd', 'permanentAdd', 'mobile', 'positionID',
            'plantillaNo', 'empStatusID', 'dateEmployed',
            'last_appointment', 'officeID'
        )
            ->get();

        $employees->transform(function ($employee) use ($positions, $offices, $employmentStatuses) {
            $employee->status = $employee->status == 1 ? 'Active' : 'Inactive';
            $employee->positionID = $positions[$employee->positionID]->designation ?? 'N/A';
            $employee->empStatusID = $employmentStatuses[$employee->empStatusID]->type ?? 'Unknown';
            $employee->officeID = isset($offices[$employee->officeID])
                ? "({$offices[$employee->officeID]->office}) {$offices[$employee->officeID]->officeName}"
                : 'N/A';

            return $employee;
        });

        return response()->json(['data' => $employees]);
    }

    public function printPdf()
    {
        $positions = DB::connection('hrmps')
            ->table('positions')
            ->select('positionID', 'designation')
            ->get()
            ->keyBy('positionID');

        $offices = DB::connection('hrmps')
            ->table('offices')
            ->select('officeID', 'officeName', 'office')
            ->get()
            ->keyBy('officeID');

        $employmentStatuses = DB::connection('hrmps')
            ->table('employment_status')
            ->select('empStatusID', 'type')
            ->get()
            ->keyBy('empStatusID');
        $startDateEmployed = '2010-01-01';
        $endDateEmployed = '2022-06-30';

        $allEmployees = DB::connection('hrmps')
            ->table('employees')
            ->select(
                'empID', 'lname', 'fname', 'mname', 'gender', 'status',
                'currentAdd', 'permanentAdd', 'mobile', 'positionID',
                'plantillaNo', 'empStatusID', 'dateEmployed',
                'last_appointment', 'officeID'
            )
            ->whereBetween('dateEmployed', [$startDateEmployed, $endDateEmployed])
            ->where('empStatusID', 3)
            ->orderByDesc('last_appointment')
            ->limit(50)
            ->get();

        $allEmployees->transform(function ($e) use ($positions, $offices, $employmentStatuses) {
            $e->status = ($e->status == 1) ? 'Active' : 'Inactive';
            $e->positionID = $positions[$e->positionID]->designation ?? 'N/A';
            $e->empStatusID = $employmentStatuses[$e->empStatusID]->type ?? 'Unknown';
            $e->officeID = isset($offices[$e->officeID])
                ? "({$offices[$e->officeID]->office}) {$offices[$e->officeID]->officeName}"
                : 'N/A';

            return $e;
        });

        $html = '
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 11px;
                color: #333;
            }
            h2 {
                text-align: center;
                margin-bottom: 10px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }
            th {
                background-color: #4CAF50;
                color: white;
                font-weight: bold;
                padding: 6px;
                border: 1px solid #ddd;
                text-align: left;
            }
            td {
                padding: 5px;
                border: 1px solid #ddd;
            }
            tr:nth-child(even) {
                background-color: #f9f9f9;
            }
            .header {
                text-align: center;
                font-size: 16px;
                margin-bottom: 20px;
            }
        </style>

        <div class="header">
            <strong>Provincial Human Resource Management and Development Office</strong><br>
            <em>Employee Report (2000–2022)</em>
        </div>

        <h2>Employee List</h2>

        <table>
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Status</th>
                    <th>Current Address</th>
                    <th>Mobile</th>
                    <th>Position</th>
                    <th>Plantilla No</th>
                    <th>Employment Status</th>
                    <th>Date Employed</th>
                    <th>Last Appointment</th>
                    <th>Office</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($allEmployees as $e) {
            $html .= '<tr>
                <td>'.e($e->empID).'</td>
                <td>'.e("{$e->lname}, {$e->fname} {$e->mname}").'</td>
                <td>'.e($e->gender).'</td>
                <td>'.e($e->status).'</td>
                <td>'.e($e->currentAdd).'</td>
                <td>'.e($e->mobile).'</td>
                <td>'.e($e->positionID).'</td>
                <td>'.e($e->plantillaNo).'</td>
                <td>'.e($e->empStatusID).'</td>
                <td>'.($e->dateEmployed ? Carbon::parse($e->dateEmployed)->format('F j, Y') : 'N/A').'</td>
                <td>'.($e->last_appointment ? Carbon::parse($e->last_appointment)->format('F j, Y') : 'N/A').'</td>
                <td>'.e($e->officeID).'</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'landscape');

        return $pdf->download('employee_list.pdf');
    }

    public function getLeaves()
    {
        $offices = DB::connection('hrmps')
            ->table('offices')
            ->select('officeID', 'officeName', 'office')
            ->get()
            ->keyBy('officeID');

        $positions = DB::connection('hrmps')
            ->table('positions')
            ->select('positionID', 'designation')
            ->get()
            ->keyBy('positionID');

        $leaveTypes = DB::connection('hrmps')
            ->table('leave_types')
            ->select('leaveID', 'leave')
            ->get()
            ->keyBy('leaveID');

        $employees = DB::connection('hrmps')
            ->table('employees')
            ->select('empID', 'lname', 'fname', 'mname', 'gender')
            ->get()
            ->keyBy('empID');

        $leaves = DB::connection('hrmps')
            ->table('leaves')
            ->select('id', 'series', 'empID', 'officeID', 'positionID', 'leaveID', 'dateFiled', 'payType', 'days')
            ->where('series', '2025')
            ->get();

        $leaveCountByType = [];
        $leaveCountByGender = ['Male' => 0, 'Female' => 0];
        $leaveCountsByTypeByGender = []; // <-- NEW

        $leaves->transform(function ($leave) use ($offices, $positions, $leaveTypes, $employees, &$leaveCountByType, &$leaveCountByGender, &$leaveCountsByTypeByGender) {

            $empID = (string) $leave->empID;
            if (isset($employees[$empID])) {
                $emp = $employees[$empID];
                $leave->employeeName = $emp->lname.', '.$emp->fname.' '.($emp->mname ?? '');
                if (strtoupper($emp->gender) === 'M') {
                    $leave->gender = 'Male';
                    $leaveCountByGender['Male']++;
                } elseif (strtoupper($emp->gender) === 'F') {
                    $leave->gender = 'Female';
                    $leaveCountByGender['Female']++;
                } else {
                    $leave->gender = 'N/A';
                }
            } else {
                $leave->employeeName = 'Unknown';
                $leave->gender = 'N/A';
            }

            $leaveName = $leaveTypes[$leave->leaveID]->leave ?? 'N/A';
            $leaveCountByType[$leaveName] = ($leaveCountByType[$leaveName] ?? 0) + 1;

            // --- NEW: count per leave type by gender ---
            if (! isset($leaveCountsByTypeByGender[$leaveName])) {
                $leaveCountsByTypeByGender[$leaveName] = ['Male' => 0, 'Female' => 0];
            }
            if ($leave->gender === 'Male') {
                $leaveCountsByTypeByGender[$leaveName]['Male']++;
            } elseif ($leave->gender === 'Female') {
                $leaveCountsByTypeByGender[$leaveName]['Female']++;
            }

            $leave->officeID = isset($offices[$leave->officeID])
                ? "({$offices[$leave->officeID]->office}) {$offices[$leave->officeID]->officeName}"
                : 'N/A';
            $leave->positionID = $positions[$leave->positionID]->designation ?? 'N/A';
            $leave->leaveID = $leaveName;
            $leave->payType = $leave->payType == 1 ? 'With Pay' : 'Without Pay';

            return $leave;
        });

        arsort($leaveCountByType);
        $mostLeaveType = key($leaveCountByType);
        $mostLeaveNumber = reset($leaveCountByType);

        return response()->json([
            'data' => $leaves,
            'summary' => [
                'leaveCountsByType' => $leaveCountByType,
                'leaveCountsByTypeByGender' => $leaveCountsByTypeByGender, // <-- NEW
                'totalMale' => $leaveCountByGender['Male'],
                'totalFemale' => $leaveCountByGender['Female'],
            ],
        ]);
    }

    public function leavesprintPdf()
    {
        // Employees and leaves
        $employees = DB::connection('hrmps')
            ->table('employees')
            ->select('empID', 'gender')
            ->get()
            ->keyBy('empID');

        $leaveTypes = DB::connection('hrmps')
            ->table('leave_types')
            ->select('leaveID', 'leave')
            ->get()
            ->keyBy('leaveID');

        $leaves = DB::connection('hrmps')
            ->table('leaves')
            ->select('empID', 'leaveID')
            ->whereBetween('series', ['2025'])
            ->get();

        // Initialize counters
        $leaveCountByType = [];
        $leaveCountByGender = ['Male' => 0, 'Female' => 0];

        foreach ($leaves as $leave) {
            // Count by leave type
            $leaveName = $leaveTypes[$leave->leaveID]->leave ?? 'N/A';
            $leaveCountByType[$leaveName] = ($leaveCountByType[$leaveName] ?? 0) + 1;

            // Count by gender
            $empID = (string) $leave->empID;
            if (isset($employees[$empID])) {
                $gender = strtoupper($employees[$empID]->gender);
                if ($gender === 'M') {
                    $leaveCountByGender['Male']++;
                }
                if ($gender === 'F') {
                    $leaveCountByGender['Female']++;
                }
            }
        }

        // Build HTML for PDF
        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 12px; }
            h2 { text-align: center; margin-bottom: 20px; }
            table { width: 50%; margin: 0 auto 20px auto; border-collapse: collapse; }
            th, td { border: 1px solid #333; padding: 8px; text-align: center; }
            th { background-color: #4CAF50; color: white; }
        </style>
        <h2>Leave Summary (2024–2025)</h2>
        
        <table>
            <thead>
                <tr><th>Gender</th><th>Total Leaves</th></tr>
            </thead>
            <tbody>
                <tr><td>Male</td><td>'.$leaveCountByGender['Male'].'</td></tr>
                <tr><td>Female</td><td>'.$leaveCountByGender['Female'].'</td></tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr><th>Leave Type</th><th>Total</th></tr>
            </thead>
            <tbody>';

        foreach ($leaveCountByType as $type => $count) {
            $html .= '<tr><td>'.e($type).'</td><td>'.e($count).'</td></tr>';
        }

        $html .= '</tbody></table>';

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

        return $pdf->download('leave_summary.pdf');
    }
}
