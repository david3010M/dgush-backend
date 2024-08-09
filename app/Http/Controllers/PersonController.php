<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexPersonRequest;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use App\Http\Requests\UpdatePersonRequest;
use App\Models\User;

class PersonController extends Controller
{
    public function index(IndexPersonRequest $request)
    {
        return $this->getFilteredResults(
            Person::class,
            $request,
            Person::filters,
            Person::sorts,
            PersonResource::class
        );
    }

    public function show()
    {
        $personId = auth()->user()->id;
        $person = Person::find($personId);
        if (!$person) return response()->json(['message' => 'Person not found'], 404);
        return response()->json(new PersonResource($person));
    }

    public function update(UpdatePersonRequest $request)
    {
        $personId = auth()->user()->id;
        $person = Person::find($personId);
        if (!$person) return response()->json(['message' => 'Person not found'], 404);
        $person->update($request->all());
        $person = Person::find($personId);
        $user = User::find(auth()->user()->id);
        $dataUser = [
            'names' => $person->names,
            'lastnames' => $person->fatherSurname . ' ' . $person->motherSurname,
            'email' => $person->email,
        ];
        $user->update($dataUser);
        return response()->json(new PersonResource($person));
    }
}
