<?php

namespace App\Http\Requests\Api\V1;

use App\Permissions\Abilities;
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
            'data.relationships.author.data.id' => 'prohibited'
        ];

        //providing granular permissions to update the ticket
        if ($this->user()->tokenCan(Abilities::UpdateTicket)) {
            $rules['data.relationships.author.data.id'] = 'sometimes|integer';
        }

        return $rules;

    }
}
