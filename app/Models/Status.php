<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model {

    protected $fillable = [];
    protected $table ='inventory_status';

    protected $dates = [];

    public static $rules = [
        // Validation rules
    ];

    // Relationships

}