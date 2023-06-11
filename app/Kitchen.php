<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Kitchen extends Authenticatable
{
	use Notifiable;
    protected $fillable=['name', 'color', 'username', 'password', 'panel_size', 'font_size', 'store_id'];
    protected $hidden=['password'];

	public function setPasswordAttribute($value) {
        $this->attributes['password'] = Hash::make($value);
    }
    public function menu_group_kitchens() {
    	return $this->hasMany('App\MenuGroupKitchen');
    }    
    public function menu_groups() {
    	return $this->belongsToMany('App\MenuGroup', 'menu_group_kitchens');
    }
}
