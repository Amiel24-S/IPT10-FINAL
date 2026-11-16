<?php
namespace App\Models;

use Spine\Model;

class Category extends Model
{
    protected static string $table = 'categories';
    protected static array $fillable = ['name'];
}
