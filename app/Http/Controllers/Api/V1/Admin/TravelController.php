<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelListRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;


class TravelController extends Controller
{
    //
    // public function validationFailure (Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json($validator->errors(), 422));
    // }

    public function store (TravelListRequest $request)
    {
       
        $newTravel = Travel::create($request->validated());

        return new TravelResource($newTravel);
    }

    public function update (Travel $travel, TravelListRequest $request)
    {
         $travel->update($request->validated());

        return new TravelResource($travel);
    }
}
