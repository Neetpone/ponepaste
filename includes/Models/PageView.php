<?php
namespace PonePaste\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model {
    protected $table = 'page_view';
    protected $fillable = ['date'];

    public $timestamps = false;
}
