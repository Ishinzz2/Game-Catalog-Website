<?php
use Illuminate\Support\Facades\Artisan;
Artisan::command('about:game-library', function () {
    $this->comment('Game Catalog — Merged Game Library');
})->purpose('Menampilkan informasi singkat proyek');
