<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourCreateRequest;
use App\Http\Requests\ToursListRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    //
    public function store (TourCreateRequest $request, Travel $travel) 
    {
        $tour = $travel->tours()->create($request->validated());

        return new TourResource($tour);
    }
}
