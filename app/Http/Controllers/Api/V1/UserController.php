<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\NotAuthorizedToEditException;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\Api\V1\ReplaceUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Http\Requests\Api\V1\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Policies\V1\UserPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends ApiController
{
    protected string $policyClass = UserPolicy::class;

    /**
     * Get All Users
     *
     * Retrieve a paginated list of all users.
     *
     * @group Users
     *
     * @param AuthorFilter $filters
     * @return AnonymousResourceCollection
     */
    public function index(AuthorFilter $filters): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::filter($filters)->paginate()
        );
    }

    /**
     * Create User
     *
     * Create a new user with the provided details.
     *
     * @group Users
     *
     * @param StoreUserRequest $request
     * @return UserResource
     * @throws NotAuthorizedToEditException
     */
    public function store(StoreUserRequest $request): UserResource
    {
        //policy
        $this->isAble('store', User::class, 'user');

        return new UserResource(User::create($request->mappedAttributes()));
    }

    /**
     * Display User
     *
     * Retrieve the details of a specific user by their ID.
     *
     * @group Users
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        if ($this->include('tickets')) {
            return new UserResource($user->load('tickets'));
        }

        return new UserResource($user);
    }


    /**
     * Path User
     *
     * Update specific fields of an existing user.
     *
     * @group Users
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return UserResource
     * @throws NotAuthorizedToEditException
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        //PATCH method to update the user

        //policy
        $this->isAble('update', $user, 'user');

        $user->update($request->mappedAttributes());

        return new UserResource($user);
    }

    /**
     * Update User
     *
     * Replace an existing user with new details.
     *
     * @group Users
     *
     * @param ReplaceUserRequest $request
     * @param User $user
     * @return UserResource
     * @throws NotAuthorizedToEditException
     */
    public function replace(ReplaceUserRequest $request, User $user): UserResource
    {
        //PUT method to replace the user

        //policy
        $this->isAble('replace', $user, 'user');

        $user->update($request->mappedAttributes());

        return new UserResource($user);
    }

    /**
     * Delete User
     *
     * Delete a specific user by their ID.
     *
     * @group Users
     *
     * @param User $user
     * @return JsonResponse
     * @throws NotAuthorizedToEditException
     */
    public function destroy(User $user): JsonResponse
    {
        //policy
        $this->isAble('delete', $user, 'user');

        $user->delete();

        return $this->success($user, 'User deleted successfully.', 204);
    }
}
