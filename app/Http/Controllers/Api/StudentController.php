<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\Api\StudentResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Support\Facades\File;

class StudentController extends Controller
{
    const BEARER_TOKEN_HEADER = 'Bearer {token}';
    protected object $user;
    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * Parent-Student List.
     *
     * @response array{data: StudentResource[], message: string}
     */
    #[HeaderParameter('Authorization', self::BEARER_TOKEN_HEADER)]
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $student = $this->user->children()->paginate(10)->appends(request()->query());

        return StudentResource::collection($student)->additional([
            'message' => 'success',
        ]);
    }

    /**
     * Bank Mutation from student.
     *
     * @response array{data: StudentResource, message: string}
     */
    #[HeaderParameter('Authorization', self::BEARER_TOKEN_HEADER)]
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
     *
     * @response array{data: object, message: string}
     * [HeaderParameter('Authorization', 'Bearer {token}')]
     */
    #[HeaderParameter('Authorization', self::BEARER_TOKEN_HEADER)]
    public function updateProfile(UpdateUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = User::where('uuid', $this->user->uuid)->first();
        $oldValue = $user->getOriginal();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password ? bcrypt($request->password) : $this->user->password,
        ]);

        if ($request->hasFile('photo')) {
            if ($this->user->parentDetail && $this->user->parentDetail->photo) {
                $photoPath = str_replace(asset('storage/'), '', $this->user->parentDetail->photo);
                $fullPath = public_path('storage/' . $photoPath);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        }
        $oldParent = $this->user->parentDetail->getOriginal();
        $parent = $this->user->parentDetail()->updateOrCreate(
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

        if ($user->getChanges()) {
            foreach ($user->getChanges() as $key => $value) {
                $oldValue[$key] = $user->getOriginal($key);
            }
            $this->createLog('Update User Api', 'Update User Api', $this->user, [
                'old_data' => $oldValue,
                'new_data' => $user->getChanges(),
            ], 'update');
        }
        if ($parent->getChanges()) {
            $this->createLog('Update Profile Api', 'Update Profile Api', $parent, [
                'old_data' => $oldParent,
                'new_data' => $parent->getChanges(),
            ], 'update');
        }

        return response()->json([
            'message' => 'success',
            'data' => $this->user->load('parentDetail'),
        ]);
    }
}
