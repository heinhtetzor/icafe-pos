<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

class MenuGroup extends Model
{
    protected $fillable = ['name'];
    public function menus () {
        return $this->hasMany('App\Menu');
    }
    public function menuGroupsWithMenus () {
        return $this;
    }
    public static function getMenuGroups() {
        return MenuGroup::orderBy('created_at')->get();
    }
    public function menu_group_kitchens() {
        return $this->hasMany('App\MenuGroupKitchen');
    }
    public function items () {
        return $this->hasMany('App\Item');
    }
    public function expense_items () {
        return $this->hasMany('App\ExpenseItem');
    }
}
