<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorTicketsController extends ApiController
{
    public function index(int $author_id, TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($author_id, StoreTicketRequest $request)
    {
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'user_id' => $author_id,
        ];

        return new TicketResource(Ticket::create($model));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $author_id, int $ticket_id): JsonResponse
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);

            //we need to check id the author id matches the user_id of the ticket
            if ($ticket->user_id === $author_id) {
                $ticket->delete();

                return $this->ok('Ticket deleted successfully.', $ticket);
            }

            return $this->error('Ticket not found', [
                'error' => 'The specified ticket id does not exist.'
            ], 404);

        } catch (ModelNotFoundException $exception) {

            return $this->error('Ticket not found', [
                'error' => 'The specified ticket id does not exist.'
            ], 404);
        }
    }
}
