<?php

namespace Manta\Traits;

trait SortableTrait
{

    public function sortingArray(string $ids): void
    {


        $rows = [];
        foreach (explode(',', $ids) as $value) {
            $rows[] = $this->rows[$value];
        }
        $this->rows = $rows;
    }
}
