<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_code',
        'name',
        'dob',
        'gender',
        'blood_group',
        'contact_number',
        'email',
        'address',
        'hospital_name',
        'doctor_name',
        'admission_date',
        'blood_required_date',
        'blood_quantity_required',
        'request_status',
        'diagnosis',
        'notes',
        'created_by',
    ];

    // Relationships
    public function createBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Blood Group relationship
    public function userBloodGroup()
    {
        return $this->belongsTo(Lov::class, 'blood_group');
    }

    // Gender relationship
    public function userGender()
    {
        return $this->belongsTo(Lov::class, 'gender');
    }
}
