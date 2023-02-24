<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class Badge extends Model {
    protected $table = 'badges';
    public $timestamps = false;

    protected $fillable = [
        'name', 'image_url'
    ];
}
