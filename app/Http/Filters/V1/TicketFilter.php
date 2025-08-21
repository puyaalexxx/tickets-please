<?php
declare(strict_types=1);


namespace App\Http\Filters\V1;

use App\Http\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Builder;

/**
 * TicketFilter class to filter tickets based on various criteria.
 */
class TicketFilter extends QueryFilter
{
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
     * Filter tickets by statuses
     * /tickets?filter[status]=C,X
     *
     * @param string $value
     * @return Builder
     */
    public function status(string $value): Builder
    {
        return $this->builder->whereIn('status', explode(',', $value));
    }

    /**
     * Filter tickets by title (substring from the title)
     * /tickets?filter[title]=*eum*
     *
     * @param string $value
     * @return Builder
     */
    public function title(string $value): Builder
    {
        $likeStr = str_replace('*', '%', $value);

        return $this->builder->where('title', 'like', $likeStr);
    }


}
