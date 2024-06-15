<?php

namespace Manta\Traits;

use Livewire\Attributes\Url;

trait WithSortingTrait
{
    #[Url]
    public $sortCol;

    #[Url]
    public $sortAsc = false;

    public function sortBy($column)
    {
        if ($this->sortCol === $column) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = false;
        }
    }

    protected function applySorting($query)
    {
        if ($this->sortCol) {
            $query->orderBy($this->sortCol, $this->sortAsc ? 'asc' : 'desc');
        }

        return $query;
    }
}
