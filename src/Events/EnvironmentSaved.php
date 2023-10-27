<?php

namespace Grilar\Installer\Events;

use Grilar\Base\Events\Event;
use Illuminate\Http\Request;

class EnvironmentSaved extends Event
{
    public function __construct(public Request $request)
    {
    }
}
