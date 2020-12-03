<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class position_tracking extends Model {

    protected $fillable = [];
    protected $table = 'position_tracking';

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships
    public function sales_manager() 
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
