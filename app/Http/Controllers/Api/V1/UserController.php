<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filters): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::filter($filters)->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): UserResource|JsonResponse
    {
        try {
            //policy
            $this->isAble('store', User::class);

            return new UserResource(User::create($request->mappedAttributes()));

        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to create the user', [], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): UserResource
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }

        return new UserResource($user);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, int $user_id): UserResource|JsonResponse
    {
        //PATCH method to update the user

        try {
            $user = User::findOrFail($user_id);

            //policy
            $this->isAble('update', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);

        } catch (ModelNotFoundException $exception) {
            return $this->error('User not found', [
                'error' => 'The specified user id does not exist.'
            ], 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to update the user', [], 401);
        }
    }

    public function replace(ReplaceUserRequest $request, int $user_id): UserResource|JsonResponse
    {
        //PUT method to replace the user

        try {
            $user = User::findOrFail($user_id);

            //policy
            $this->isAble('replace', $user);

            $user->update($request->mappedAttributes());

            return new UserResource($user);

        } catch (ModelNotFoundException $exception) {
            return $this->error('User not found', [
                'error' => 'The specified user id does not exist.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $user_id): JsonResponse
    {
        try {
            $user = User::findOrFail($user_id);

            //policy
            $this->isAble('delete', $user);

            $user->delete();

            return $this->ok('User deleted successfully.', $user);
        } catch (ModelNotFoundException $exception) {

            return $this->error('User not found', [
                'error' => 'The specified user id does not exist.'
            ], 404);
        }
    }
}
