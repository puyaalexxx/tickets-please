<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\Abilities;
use Illuminate\Contracts\Validation\ValidationRule;

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
        $authorIdAttribute = $this->routeIs('tickets.store') ? 'data.relationships.author.data.id' : 'author';
        //get current logged-in user (store method don't need the author id from url)
        $user = $this->user();
        $authorRule = 'required|integer|exists:users,id';

        $rules = [
            'data.attributes.title' => 'required|string',
            'data.attributes.description' => 'required|string',
            'data.attributes.status' => 'required|string|in:A,C,H,X',
            $authorIdAttribute => $authorRule . '|size:' . $user->id
        ];

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
}
