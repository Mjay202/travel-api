<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourContoller extends Controller
{
    //

    public function index (Travel $travel)
    {
        return $travel->tours();
    }
}
