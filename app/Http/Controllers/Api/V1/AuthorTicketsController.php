<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorTicketsController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    public function index(int $author_id, TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request)
    {
        try {
            //policy
            $this->isAble('store', Ticket::class);

            return new TicketResource(Ticket::create($request->mappedAttributes([
                'author' => 'user_id'
            ])));

        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to create the ticket', [], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, int $author_id, int $ticket_id): TicketResource|JsonResponse
    {
        //PATCH method to update the ticket

        try {
            //we need to check id the author id matches the user_id of the ticket
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            //policy
            $this->isAble('update', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', [
                'error' => 'The specified ticket id does not exist.'
            ], 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to update the ticket', [], 401);
        }
    }

    public function replace(ReplaceTicketRequest $request, int $author_id, int $ticket_id): TicketResource|JsonResponse
    {
        //PUT method to replace the ticket

        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            //policy
            $this->isAble('replace', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', [
                'error' => 'The specified ticket id does not exist.'
            ], 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to update the ticket', [], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $author_id, int $ticket_id): JsonResponse
    {
        try {
            $ticket = Ticket::where('id', $ticket_id)
                ->where('user_id', $author_id)
                ->firstOrFail();

            //policy
            $this->isAble('delete', $ticket);

            $ticket->delete();

            return $this->ok('Ticket deleted successfully.', $ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket not found', [
                'error' => 'The specified ticket id does not exist.'
            ], 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to delete the ticket', [], 401);
        }
    }
}
