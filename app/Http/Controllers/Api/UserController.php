<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Sanctum\Sanctum;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validateNewUser = $request->validate([
            'name' => ['required'],
            'username' => ['required', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'jmbg' => ['required', 'min:13', 'max:13', Rule::unique('users', 'jmbg'), 'regex:/^[0-9]+$/'],
            'password' => ['required', 'min:8', 'max:16'],
            'user_type' => ['required', Rule::in([User::USER_TYPE_LIBRARIAN, User::USER_TYPE_STUDENT])],
            'picture' => ['file', 'max:5120'],
        ]);

        $user = User::create($validateNewUser);

        // Send password reset email immediately after creating the user
        $token = \Illuminate\Support\Facades\Password::createToken($user);
        $user->sendPasswordResetNotification($token);

        // If the request contains a profile picture, store it
        if ($request->hasFile('picture')) {
            $user->addMedia($request->file('picture'))->toMediaCollection('profile_picture');
        }

        return response()->json(['message' => 'User registered successfully, reset email sent.', $user], 201);
    }

    public function login(Request $request)
    {

        $validateLogin = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'max:32'],
        ]);

        if (auth()->attempt($validateLogin)) {
            $user = User::where('email', $validateLogin['email'])->first();
            $token = $user->createToken('API TOKEN')->plainTextToken;

            return $token;
        } else {
            return response()->json(['error' => 'Invalid credentials'], 422);
        }
    }

    public function logout(Request $request)
    {
        if ($token = $request->bearerToken()) {
            $model = Sanctum::$personalAccessTokenModel;
            $accessToken = $model::findToken($token);
            $accessToken->delete();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User Logged out',
        ], 200);
    }

    public function viewUsers(Request $request, $role)
    {
        $perPage = $request->input('per_page', 20);
        $searchValue = $request->input('search_value');

        $users = User::where('user_type', $role)
            ->where(function ($query) use ($searchValue) {
                $query->where('name', 'like', '%'.strtolower($searchValue).'%')
                    ->orWhere('email', 'like', '%'.strtolower($searchValue).'%')
                    ->orWhere('username', 'like', '%'.strtolower($searchValue).'%');
            })
            ->paginate($perPage);

        return response()->json($users);
    }

    public function viewUser(User $user)
    {

        if (auth()->user()->user_type !== User::USER_TYPE_LIBRARIAN) {
            return response()->json(['error' => 'Unauthorized'], 422);
        }

        return response()->json($user);
    }

    public function viewUserProfilePicture(User $user)
    {
        return $user->getFirstMediaUrl('profile_picture');
    }

    public function editUser(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'username' => ['sometimes', 'required', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['sometimes', 'required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'jmbg' => ['sometimes', 'required', 'min:13', 'max:13', 'regex:/^[0-9]+$/', Rule::unique('users', 'jmbg')->ignore($user->id)],
            'password' => ['sometimes', 'required', 'min:8', 'max:16'],
            'picture' => ['sometimes', 'file', 'max:5120', 'mimes:jpg,'],
        ]);

        if ($request->has('name')) {
            $user->name = $validatedData['name'];
        }
        if ($request->has('username')) {
            $user->username = $validatedData['username'];
        }
        if ($request->has('email')) {
            $user->email = $validatedData['email'];
        }
        if ($request->has('jmbg')) {
            $user->jmbg = $validatedData['jmbg'];
        }
        if ($request->has('password')) {
            $user->password = $validatedData['password'];
        }

        if ($request->hasFile('picture')) {
            $user->clearMediaCollection('profile_picture');
            $user->addMedia($request->file('picture'))->toMediaCollection('profile_picture');
        }

        $user->save();

        return response()->json(['message' => 'User details updated successfully', $user]);
    }

    public function delete(User $user)
    {
        $requestingUser = Auth::user();
        if ($requestingUser->user_type !== User::USER_TYPE_LIBRARIAN) {
            throw new UnauthorizedException("You don't have permission to delete user accounts");
        }

        if ($user->hasMedia('profile_picture')) {
            $user->clearMediaCollection('profile_picture');
        }

        $user->delete();

        return response()->json(['message' => 'User account deleted successfully']);
    }
}
