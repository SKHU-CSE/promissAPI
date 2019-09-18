<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
class Waiting extends Model
{
    protected $table = 'Waiting';
    protected $fillable = [
        'appointment_id','user_id','date',
    ];
    public $timestamps=false;
    public function appointments()
    {
        return $this->belongsTo(Appointment::class);
    }
}
