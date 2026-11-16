<?php

use App\Http\Controllers\Mcp\McpController;
use App\Http\Middleware\AuthenticateMcpToken;
use App\Http\Middleware\EnsureMcpEnabled;
use Illuminate\Support\Facades\Route;

Route::match(['get', 'post', 'delete'], '/mcp', McpController::class)
    ->middleware([EnsureMcpEnabled::class, AuthenticateMcpToken::class]);
