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
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters): AnonymousResourceCollection
    {
        return TicketResource::collection(Ticket::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     * @throws NotAuthorizedToEditException
     */
    public function store(StoreTicketRequest $request): TicketResource|JsonResponse
    {
        //policy
        if ($this->isAble('store', Ticket::class)) {
            return new TicketResource(Ticket::create($request->mappedAttributes()));
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): TicketResource|JsonResponse
    {
        if ($this->include('author')) {
            return new TicketResource($ticket->load('user'));
        }

        return new TicketResource($ticket);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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
