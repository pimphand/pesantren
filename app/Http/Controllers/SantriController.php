<?php

namespace App\Http\Controllers;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\SantriResource;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Http\Requests\StoreSantriRequest;
use App\Http\Requests\UpdateSantriRequest;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('santri', [
            'title' => 'Santri',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSantriRequest $request)
    {
        $santri = User::create(array_merge($request->validated(), [
            'uuid'     => Str::uuid(),
            'parent_id'     => $request->parent_id,
            'phone'    => $request->phone,
            'password' => bcrypt($request->password),
            'pin'      => bcrypt($request->pin),
        ]))->addRole('santri');

        $student = Student::create([
            'user_id' => $santri->id,
            'class_now' => $request->class_now ?? null,
            'address' => $request->address ?? null,
            'level' => $request->level ?? null,
            'date_of_birth' => $request->date_of_birth ?? null,
            'place_of_birth' => $request->place_of_birth ?? null,
            'gender' => $request->gender ?? null,
            'admission_number' => $request->nsm ?? null,
            'national_admission_number' => $request->nisn ?? null,
        ]);

        $this->createLog('Santri', 'Create Santri', $santri, [
            'old_data' => null,
            'new_data' => $santri->toArray(),
        ], 'create');

        return response()->json([
            'message' => 'Santri berhasil ditambahkan',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $santri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $santri)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSantriRequest $request, User $santri)
    {
        $oldSantri = $santri->getOriginal();
        $santri->update(array_merge($request->validated(), [
            'password' => $request->password ? bcrypt($request->password) : $santri->password,
            'phone' => $request->phone,
            'parent_id'     => $request->parent_id,
        ]));

        $student = Student::where('user_id', $santri->id)->update([
            'address' => $request->address ?? null,
            'class_now' => $request->class_now ?? null,
            'level' => $request->level ?? null,
            'date_of_birth' => $request->date_of_birth ?? null,
            'place_of_birth' => $request->place_of_birth ?? null,
            'gender' => $request->gender ?? null,
            'admission_number' => $request->nsm ?? null,
            'national_admission_number' => $request->nisn ?? null,
        ]);

        $this->createLog('Santri', 'Update Santri', $santri, [
            'old_data'  => $oldSantri,
            'new_data'  => $santri->getChanges(),
        ], 'update');

        return response()->json([
            'message' => 'Santri berhasil diperbarui',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $santri)
    {
        $oldSantri = $santri->getOriginal();
        $santri->delete();

        $this->createLog('Santri', 'Delete Santri', $santri, [
            'old_data' => $oldSantri,
            'new_data' => $santri->toArray(),
        ], 'delete');

        return response()->json([
            'message' => 'Santri berhasil dihapus',
        ]);
    }

    public function data(): AnonymousResourceCollection
    {
        $santri = QueryBuilder::for(User::class)
            ->withRole('santri')
            ->allowedSorts(['name'])
            ->allowedFilters(['name'])
            ->defaultSort('-name')
            ->paginate(request()->input('per_page') ?? 10)
            ->appends(request()->query());

        return SantriResource::collection($santri);
    }
}
