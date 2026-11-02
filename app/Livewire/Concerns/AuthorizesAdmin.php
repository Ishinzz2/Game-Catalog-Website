<?php

namespace App\Livewire\Concerns;

trait AuthorizesAdmin
{
    protected function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
