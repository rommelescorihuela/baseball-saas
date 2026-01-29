<?php

use App\Models\Team;
use App\Models\League;

function current_team(): ?Team
{
    return app()->bound('currentTeam')
        ? app('currentTeam')
        : null;
}

function current_league(): ?League
{
    return app()->bound('currentLeague')
        ? app('currentLeague')
        : null;
}
