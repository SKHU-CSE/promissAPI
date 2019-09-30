<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Result extends Model
{
    protected $table = 'ResultTable';
    protected $fillable = [
        'appointment_id','user_id','appointment_radius','latitude','longitude','Fine_current','id',
    ];
    public $timestamps=false;
    public function appointments()
    {
        return $this->belongsTo(Appointment::class);
    }
}
