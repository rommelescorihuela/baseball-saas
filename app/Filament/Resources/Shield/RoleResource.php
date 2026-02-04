<?php

namespace App\Filament\Resources\Shield;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource as BaseRoleResource;
use Filament\Panel;

class RoleResource extends BaseRoleResource
{
    protected static ?string $model = null; // Let base class handle this via Utils

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }
}