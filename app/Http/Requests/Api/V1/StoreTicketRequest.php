<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\Abilities;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class StoreTicketRequest extends BaseTicketRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $ticketController = $this->routeIs('tickets.store');
        $authorIdAttribute = $ticketController ? 'data.relationships.author.data.id' : 'author';
        //get current logged-in user (store method don't need the author id from url)
        $user = Auth::user();
        $authorRule = 'required|integer|exists:users,id';

        $rules = [
            'data' => 'required|array',
            'data.attributes' => 'required|array',
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
        ];

        //for scribe api docs
        if ($ticketController) {
            $rules['data.relationships'] = 'required|array';
            $rules['data.relationships.author'] = 'required|string';
            $rules['data.relationships.author.data'] = 'required|array';
        }

        $rules[$authorIdAttribute] = $authorRule . '|size:' . $user->id;

        if ($user->tokenCan(Abilities::CreateTicket)) {
            $rules[$authorIdAttribute] = $authorRule;
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // If the request is for storing a new ticket, ensure the author ID is provided
        // This is only necessary when creating a new ticket from the specific author id route
        if ($this->routeIs('authors.tickets.store')) {
            $this->merge([
                'author' => $this->route('author')
            ]);
        }
    }

    /**
     * Define the body parameters for API documentation (Scribe)
     *
     * @return array
     */
    public function bodyParameters(): array
    {
        $documentation = [
            'data.attributes.title' => [
                'description' => 'The title of the ticket.',
                'example' => 'No-example',
            ],
            'data.attributes.description' => [
                'description' => 'The description of the ticket.',
                'example' => 'No-example',
            ],
            'data.attributes.status' => [
                'description' => 'The status of the ticket. Allowed values: A (Active), C (Closed), H (On Hold), X (Cancelled).',
                'example' => 'No-example',
            ],

        ];

        if ($this->routeIs('tickets.store')) {
            $documentation['data.relationships.author.data.id'] = [
                'description' => 'The ID of the author creating the ticket.',
                'example' => 1,
            ];
        } else {
            //this for the AuthorTicketsController store method
            $documentation['author'] = [
                'description' => 'The author assigned to the ticket.',
                'example' => 'No-example',
            ];
        }

        return $documentation;
    }
}
