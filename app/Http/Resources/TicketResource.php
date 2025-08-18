<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $title
 * @property mixed $id
 * @property mixed $description
 * @property mixed $status
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class TicketResource extends JsonResource
{
    // Uncomment the following line if you want to wrap the resource in toickets instead of data
    // public static $wrap = 'ticket';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'ticket',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status,
                'created_at' => $this->created_at?->toIso8601String(),
                'updated_at' => $this->updated_at?->toIso8601String(),
            ],
            'relationships' => [
                'author' => [
                    'data' => /*$this->whenLoaded('author', function () {*/
                        [
                            'type' => 'user',
                            'id' => $this->user_id,
                        ],
                    'links' => [
                        'self' => 'todo',
                    ]
                ]
                // }),
            ],
            'links' => [
                'self' => route('tickets.show', $this->id),
            ],
        ];
    }
}
