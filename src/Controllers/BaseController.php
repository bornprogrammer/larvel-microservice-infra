<?php

namespace Laravel\Infrastructure\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as LaravelBaseController;
use JetBrains\PhpStorm\ArrayShape;
use Laravel\infrastructure\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Laravel\infrastructure\Helpers;

class BaseController extends LaravelBaseController
{
    // leaving empty for future functinality
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    #[ArrayShape(["isHealthy" => "bool"])]
    public function healthCheck(Request $request): array
    {
        return ["isHealthy" => true];
    }
}
