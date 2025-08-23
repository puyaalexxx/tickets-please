<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\AuthorFilter;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorsController extends ApiController
{
    /**
     * Get All Authors
     *
     * Retrieve a paginated list of all authors who have created tickets. Used in include parameter.
     *
     * @group Authors
     *
     * @param AuthorFilter $filters
     * @return AnonymousResourceCollection
     */
    public function index(AuthorFilter $filters): AnonymousResourceCollection
    {
        return UserResource::collection(
            User::select('users.*')
                ->join('tickets', 'users.id', '=', 'tickets.user_id')
                ->filter($filters)
                ->distinct()
                ->paginate()
        );
    }

    /**
     * Display Author
     *
     * Retrieve the details of a specific author by their ID used in include parameter.
     *
     * @group Authors
     *
     * @param User $author
     * @return UserResource
     */
    public function show(User $author): UserResource
    {
        if ($this->include('tickets')) {
            return new UserResource($author->load('tickets'));
        }

        return new UserResource($author);
    }
}
