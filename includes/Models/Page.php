<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model {
    protected $table = 'pages';
    protected $primaryKey = 'page_name';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}