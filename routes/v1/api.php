<?php
declare(strict_types=1);

use App\Http\Controllers\Api\V1\TicketController;
use Illuminate\Support\Facades\Route;

Route::apiResource('/tickets', TicketController::class);
