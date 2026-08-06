<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class employeeData extends Model
{
    use HasFactory;

    protected $table = 'employeeData';

    protected $fillable = [
        'empID',
        'lname',
        'fname',
        'mname',
        'gender',
        'status',
        'currentAdd',
        'permanentAdd',
        'mobile',
        'positionID',
        'plantillaNo',
        'empStatusID',
        'dateEmployed',
        'last_appointment',
        'officeID',
    ];
}
