<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $table = 'appointment';
    protected $fillable = [
        'id','status','name','address','detail','latitude','longitude','date','date_time','radius','Fine_money','Fine_time','Fine_current','main_user_id',
    ];
    public $timestamps=false;
    /**
     * @var int
     */
    public function waitings()
    {
        return $this->hasMany(Waiting::class);
    }
    public function Members(){
        return $this->hasMany(Member::class);
    }
}
