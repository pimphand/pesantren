<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\StudentResource;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

class StudentController extends Controller
{
    protected object $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * List Student Parents.
     * @response array{data: StudentResource[], message: string}
     */
    #[HeaderParameter('Authorization', 'Bearer {token}')]
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $student = $this->user->children()->paginate(10)->appends(request()->query());
        return StudentResource::collection($student)->additional([
            'message' => 'success',
        ]);
    }

    /**
     * Bank Mutation from student.
     * @response array{data: StudentResource, message: string}
     */
    #[HeaderParameter('Authorization', 'Bearer {token}')]
    public function bankMutation(string $id): StudentResource
    {
        $student = $this->user->children()->where('uuid', $id)->first();
        $student->load('balanceHistories');

        return StudentResource::make($student)->additional([
            'message' => 'success',
        ]);
    }

    /**
     * Update profile.
     * @response array{data: object, message: string}
     */
    #[HeaderParameter('Authorization', 'Bearer {token}')]
    public function updateProfile(UpdateUserRequest $request): JsonResponse
    {
        $this->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password ? bcrypt($request->password) : $this->user->password,
        ]);

        if ($request->hasFile('photo')) {
            // Check if the user has an existing photo and delete it
            if ($this->user->parentDetail && $this->user->parentDetail->photo) {
                $photoPath = str_replace(asset('storage/'), '', $this->user->parentDetail->photo);
                $fullPath = public_path('storage/' . $photoPath);

                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        }

        $this->user->parentDetail()->updateOrCreate(
            ['user_id' => $this->user->id], // Condition to find the record
            [
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->province,
                'zip' => $request->zip,
                'photo' => $request->hasFile('photo')
                    ? asset('storage/' . $request->file('photo')->store('parent-student', 'public'))
                    : ($this->user->parentDetail ? $this->user->parentDetail->photo : null), // Retains existing photo
            ]
        );
        return response()->json([
            'message' => 'success',
            'data' => $this->user->load('parentDetail'),
        ]);
    }
}
