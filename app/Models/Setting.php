<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $guarded = [];
    public static function isCompetetionStart()
    {
        $setting = Setting::where('key', 'competetion_start')->first();
        return $setting->value == 1;
    }
    public static function isCompetetionEnd()
    {
        $setting = Setting::where('key', 'competetion_start')->first();
        return $setting->value == 2;
    }
}
