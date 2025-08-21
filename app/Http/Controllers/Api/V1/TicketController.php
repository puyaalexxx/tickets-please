<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters): AnonymousResourceCollection
    {
        /*if ($this->include('author')) {
            return TicketResource::collection(Ticket::with('user')->paginate());
        }

        return TicketResource::collection(Ticket::paginate());*/

        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): TicketResource|JsonResponse
    {
        try {
            $user = User::findOrFail($request->input('data.relationships.author.data.id'));
        } catch (ModelNotFoundException $exception) {
            return $this->error('User not found', [
                'error' => 'The specified user id does not exist.'
            ], 404);
        }

        return new TicketResource($request->mappedAttributes());
    }

    /**
     * Display the specified resource.
     */
    public function show(int $ticket_id): TicketResource|JsonResponse
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            if ($this->include('author')) {
                return new TicketResource($ticket->load('user'));
            }

            return new TicketResource($ticket);
        } catch (ModelNotFoundException $exception) {

            return $this->error('Ticket not found', [
                'error' => 'The specified ticket id does not exist.'
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, int $ticket_id): TicketResource|JsonResponse
    {
        //PATCH method to update the ticket

        try {
            $ticket = Ticket::findOrFail($ticket_id);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', [
                'error' => 'The specified ticket id does not exist.'
            ], 404);
        }
    }

    public function replace(ReplaceTicketRequest $request, int $ticket_id): TicketResource|JsonResponse
    {
        //PUT method to replace the ticket

        try {
            $ticket = Ticket::findOrFail($ticket_id);

            $model = [
                'title' => $request->input('data.attributes.title'),
                'description' => $request->input('data.attributes.description'),
                'status' => $request->input('data.attributes.status'),
                'user_id' => $request->input('data.relationships.author.data.id'),
            ];

            $ticket->update($model);

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', [
                'error' => 'The specified ticket id does not exist.'
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $ticket_id): JsonResponse
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            $ticket->delete();

            return $this->ok('Ticket deleted successfully.', $ticket);
        } catch (ModelNotFoundException $exception) {

            return $this->error('Ticket not found', [
                'error' => 'The specified ticket id does not exist.'
            ], 404);
        }
    }
}

/*$model = [
    'title' => $request->input('data.attributes.title'),
    'description' => $request->input('data.attributes.description'),
    'status' => $request->input('data.attributes.status'),
    'user_id' => $request->input('data.relationships.author.data.id'),
];*/
