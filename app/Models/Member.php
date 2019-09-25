<?php


namespace App\Models;



use Illuminate\Database\Eloquent\Model;
class Member extends Model
{

    protected $table = 'Member';
    protected $fillable = [
        'appointment_id','user_id','Fine_current','Fine_final','distance','success',
    ];
    public $timestamps=false;
    public function appointments()
    {
        return $this->belongsTo(Appointment::class);
    }
}
