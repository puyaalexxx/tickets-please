<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\NotAuthorizedToEditException;
use App\Exceptions\TicketNotFoundException;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorTicketsController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;
    private TicketService $_ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->_ticketService = $ticketService;
    }

    /**
     * Get All Tickets
     *
     * Retrieve a paginated list of all tickets for a specific author.
     *
     * @group Author Tickets
     *
     * @param int $author_id
     * @param TicketFilter $filters
     * @return AnonymousResourceCollection
     */
    public function index(int $author_id, TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(Ticket::where('user_id', $author_id)->filter($filters)->paginate());
    }

    /**
     * Create Ticket
     *
     * Create a new ticket with the provided details for a specific author.
     *
     * @group Tickets
     *
     * @param StoreTicketRequest $request
     * @return TicketResource
     * @throws NotAuthorizedToEditException
     */
    public function store(StoreTicketRequest $request): TicketResource
    {
        //policy
        $this->isAble('store', Ticket::class);

        return new TicketResource(Ticket::create($request->mappedAttributes([
            'author' => 'user_id'
        ])));
    }

    /**
     * Path Ticket
     *
     * Update specific fields from an existing ticket for a specific author.
     *
     * @group Author Tickets
     *
     * @param UpdateTicketRequest $request
     * @param int $author_id
     * @param int $ticket_id
     * @return TicketResource
     * @throws NotAuthorizedToEditException
     * @throws TicketNotFoundException
     */
    public function update(UpdateTicketRequest $request, int $author_id, int $ticket_id): TicketResource
    {
        //PATCH method to update the ticket

        //use this instead of the above one to throw a not authorized exception first
        $ticket = $this->_ticketService->findTicketForUserOrFail($ticket_id, $author_id);

        //policy
        $this->isAble('update', $ticket);

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Update Ticket
     *
     * Update an existing ticket for a specific author.
     *
     * @group Author Tickets
     *
     * @param ReplaceTicketRequest $request
     * @param int $author_id
     * @param int $ticket_id
     * @return TicketResource
     * @throws NotAuthorizedToEditException
     * @throws TicketNotFoundException
     */
    public function replace(ReplaceTicketRequest $request, int $author_id, int $ticket_id): TicketResource
    {
        //PUT method to replace the ticket
        $ticket = $this->_ticketService->findTicketForUserOrFail($ticket_id, $author_id);

        //policy
        $this->isAble('replace', $ticket);

        $ticket->update($request->mappedAttributes());

        return new TicketResource($ticket);
    }

    /**
     * Delete Ticket
     *
     * Delete ticket for a specific author.
     *
     * @group Author Tickets
     *
     * @param int $author_id
     * @param int $ticket_id
     * @return JsonResponse
     * @throws NotAuthorizedToEditException
     * @throws TicketNotFoundException
     */
    public function destroy(int $author_id, int $ticket_id): JsonResponse
    {
        $ticket = $this->_ticketService->findTicketForUserOrFail($ticket_id, $author_id);

        //policy
        $this->isAble('delete', $ticket);

        $ticket->delete();

        return $this->success($ticket, 'Ticket deleted successfully.', 204);
    }
}
