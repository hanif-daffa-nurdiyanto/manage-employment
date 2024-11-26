<?php

use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use Illuminate\Support\Facades\Route;

Route::get('/employees', function () {
    $employees = Employee::orderBy('lastname', 'DESC')->get();

    return EmployeeResource::collection($employees);
});