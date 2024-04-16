<?php

namespace App\Dto\User;

use App\Dto\QueryDto;
use App\Search\SearchModel;

class SearchUser extends SearchModel implements QueryDto
{
    private ?string $slug = null;

    // Other properties and methods...

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }
}