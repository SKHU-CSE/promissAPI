<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment';
    protected $fillable = [
        'id','status','address','detail','latitude','longitude','date','date_time','radius','Fine_money','Fine_time','main_user_id',
    ];
    public $timestamps=false;
    public function wattings()
    {
        return $this->hasMany(Waitting::class);
    }
    public function Members(){
        return $this->hasMany(Member::class);
    }
}
