<?php
declare(strict_types=1);


namespace App\Http\Filters\V1;

use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * TicketFilter class to filter tickets based on various criteria.
 */
class AuthorFilter extends QueryFilter
{
    /**
     * The attributes that can be sorted.
     *
     * @var array<string, string>
     */
    protected array $sortable = [
        'name',
        'email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ];

    /**
     * Filter tickets by created at dates
     *
     * @param string $value
     * @return Builder
     */
    public function createdAt(string $value): Builder
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('created_at', $dates);
        }

        // Assuming $value is a date string, we can use whereDate to filter by creation date
        return $this->builder->whereDate('created_at', $value);
    }

    /**
     * Filter tickets by updated at dates
     *
     * @param string $value
     * @return Builder
     */
    public function updatedAt(string $value): Builder
    {
        $dates = explode(',', $value);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }

        // Assuming $value is a date string, we can use whereDate to filter by creation date
        return $this->builder->whereDate('updated_at', $value);
    }

    public function include(string $value): Builder
    {
        return $this->builder->with($value);
    }

    /**
     * Filter tickets by ids
     * /tickets?filter[id]=1,6,9
     *
     * @param string $value
     * @return Builder
     */
    public function id(string $value): Builder
    {
        return $this->builder->whereIn('id', explode(',', $value));
    }

    /**
     * Filter tickets by email (substring from the email)
     * /tickets?filter[title]=*eum*
     *
     * @param string $value
     * @return Builder
     */
    public function email(string $value): Builder
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder->where('title', 'like', $likeStr);
    }

    /**
     * Filter tickets by name (substring from the name)
     * /tickets?filter[title]=*eum*
     *
     * @param string $value
     * @return Builder
     */
    public function name(string $value): Builder
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder->where('name', 'like', $likeStr);
    }
}
