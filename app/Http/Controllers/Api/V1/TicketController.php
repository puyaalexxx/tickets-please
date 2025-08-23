<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\NotAuthorizedToEditException;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\Api\V1\ReplaceTicketRequest;
use App\Http\Requests\Api\V1\StoreTicketRequest;
use App\Http\Requests\Api\V1\UpdateTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TicketController extends ApiController
{
    protected string $policyClass = TicketPolicy::class;

    /**
     * Get All Tickets
     *
     * Retrieve a paginated list of all tickets.
     *
     * @group Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas.
     * Dash means DESC order. No dash means ASC order.
     * Example: sort=created_at,-title
     * @queryParam filter[status] string Filter tickets by status. Example: filter[status]=A,C,X,H
     * @queryParam filter[title] string Filter tickets by title. Example: filter[title]=*id*
     * @queryParam filter[createdAt] string Filter tickets by created at date. Example: filter[createdAt]=2025-08-15,2025-08-19
     * @queryParam filter[updatedAt] string Filter tickets by updated at date. Example: filter[updatedAt]=2025-08-15,2025-08-19
     * @queryParam include string Include related resources. Example: include=author
     *
     */
    public function index(TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Create Ticket
     *
     * Create a new ticket with the provided details.
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
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttributes()));
        }
    }

    /**
     * Display Ticket
     *
     * Retrieve details of a specific ticket.
     *
     * @group Tickets
     *
     * @param Ticket $ticket
     * @return TicketResource
     */
    public function show(Ticket $ticket): TicketResource
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('user'));
        }

        return new TicketResource($ticket);
    }

    /**
     * Path Ticket
     *
     * Update specific fields of an existing ticket.
     *
     * @group Tickets
     *
     * @param UpdateTicketRequest $request
     * @param Ticket $ticket
     * @return TicketResource
     * @throws NotAuthorizedToEditException
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): TicketResource
    {
        //PATCH method to update the ticket

        //policy
        if ($this->isAble('update', $ticket)) {
            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        }
    }

    /**
     * Update Ticket
     *
     * Update an existing ticket.
     *
     * @group Tickets
     *
     * @param ReplaceTicketRequest $request
     * @param Ticket $ticket
     * @return TicketResource
     * @throws NotAuthorizedToEditException
     */
    public function replace(ReplaceTicketRequest $request, Ticket $ticket): TicketResource
    {
        //PUT method to replace the ticket

        //policy
        if ($this->isAble('replace', $ticket)) {

            $ticket->update($request->mappedAttributes());

            return new TicketResource($ticket);
        }
    }

    /**
     * Delete Ticket
     *
     * Delete a specific ticket.
     *
     * @group Tickets
     *
     * @param Ticket $ticket
     * @return JsonResponse
     * @throws NotAuthorizedToEditException
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        //policy
        if ($this->isAble('delete', $ticket)) {
            $ticket->delete();

            return $this->success($ticket, 'Ticket deleted successfully.', 200);
        }

    }
}
