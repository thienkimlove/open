<?php

namespace App\Models;

use Sentinel;

trait HasActionColumn
{
    public function generateActionColumn($item)
    {
        if (! method_exists($this, 'getActionColumnPermissions')) {
            return [];
        }

        $actionColumn = [];

        $currentUser = Sentinel::getUser();

        $permissions = $this->getActionColumnPermissions($item);

        foreach ($permissions as $key => $value) {
            if ($currentUser->hasAccess($key)) {
                $actionColumn[] = $value;
            }
        }

        return implode(' ', $actionColumn);
    }
}