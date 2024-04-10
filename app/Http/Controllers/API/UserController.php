<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


/**
 * @OA\Get(
 *     path="/api/users",
 *     tags={"Users"},
 *     summary="Get all users",
 *     description="Returns a list of all users.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/user/{id}",
 *     tags={"Users"},
 *     summary="Get user by ID",
 *     description="Returns a specific user by ID.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/users",
 *     tags={"Users"},
 *     summary="Create a new user",
 *     description="Creates a new user with the provided data.",
 *     security={{"bearerAuth": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created successfully",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Update an existing user",
 *     description="Updates an existing user with the provided data.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(ref="#/components/schemas/User")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     tags={"Users"},
 *     summary="Delete a user",
 *     description="Deletes an existing user by ID.",
 *     security={{"bearerAuth": {}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the user",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */


class UserController extends Controller
{


    public function assignRole(Request $request)
    {
        // Check if the authenticated user is an admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the incoming request data
        $request->validate([
            'user_id' => 'required|integer',
            'role_name' => 'required|string',
        ]);

        // Retrieve the user instance based on the provided ID
        $user = User::findOrFail($request->user_id);


        // Retrieve the role by name
        $role = Role::where('name', $request->role_name)->first();

        if (!$role) {
            // Role not found, return an error response
            return response()->json(['error' => 'Role not found'], 404);
        }

        // Attach the role to the user
        $user->role()->associate($role)->save();

        // Return a success response
        return response()->json([
            'message' => 'Role assigned successfully',
            'user' => $user,
        ], 200);
    }

//Méthode pour afficher tous les utilisateurs
//http://localhost:8000/api/users
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('role')->get();
        return response()->json($users);
    }

//    public function index()
//    {
//        $users = User::all();
//        return response()->json($users);
//    }


    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('view', $user);
        return response()->json($user);
    }



    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        // Validate request data
        $request->validate([
            'name' => 'required|string', // Ensure 'name' is required
            'email' => 'required|string|unique:users',
            'password' => 'required|string',
        ]);

        // Retrieve the default role ("user")
        $defaultRole = Role::where('name', 'user')->first();

        // If the default role doesn't exist, you may handle it as needed
        if (!$defaultRole) {
            return response()->json(['error' => 'Default role not found.'], 500);
        }

        // Extract user data from the request
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];

        // Create the user with the default role attached
        $user = $defaultRole->users()->create($userData);

        // Return success response with created user data
        return response()->json(['user' => $user, 'message' => 'User created successfully.'], 201);
    }



    // Méthode pour mettre à jour un utilisateur existant
//    http://localhost:8000/api/users/{id}


    public function update1(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        $userData = $request->except('password');

        if ($request->has('password')) {
            $userData['password'] = Hash::make($request->get('password'));
        }
        $user->update($userData);

        return response()->json($user, 200);
    }

    public function update(Request $request, $id)
    {
       // dd($request);
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        $userData = $request->except('password', 'role_name');

        if ($request->has('password')) {
            $userData['password'] = Hash::make($request->get('password'));
        }

        if ($request->has('role_name')) {
            // Validate the incoming request data
            $request->validate([
                'role_name' => 'required|string',
            ]);

            // Retrieve the role by name
            $role = Role::where('name', $request->role_name)->first();

            if (!$role) {
                // Role not found, return an error response
                return response()->json(['error' => 'Role not found'], 404);
            }

            // Attach the role to the user
            $user->role()->associate($role)->save();

            return response()->json([
                'message' => 'Role assigned successfully',
                'user' => $user,
            ], 200);
        }

        // Update the user's information
        $user->update($userData);

        return response()->json($user, 200);
    }


    // Méthode pour supprimer un utilisateur


    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('delete', $user);

        $user->delete();
        return response()->json(["message" => "User has been deleted successfully."], 200);
    }
}
