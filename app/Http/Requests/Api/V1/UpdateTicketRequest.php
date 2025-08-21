<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateTicketRequest extends BaseTicketRequest
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
        $rules = [
            'data.attributes.title' => 'sometimes|string',
            'data.attributes.description' => 'sometimes|string',
            'data.attributes.status' => 'sometimes|string|in:A,C,H,X',
        ];

        // If the request is for storing a new ticket, ensure the author ID is provided
        // This is only necessary when creating a new ticket from the specific author id route
        if ($this->routeIs('tickets.store')) {
            $rules['data.relationships.author.data.id'] = 'sometimes|integer';
        }

        return $rules;

    }
}
