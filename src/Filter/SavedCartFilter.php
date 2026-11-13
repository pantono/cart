<?php

namespace Pantono\Cart\Filter;

use Pantono\Contracts\Filter\PageableInterface;
use Pantono\Database\Traits\Pageable;
use Pantono\Authentication\Model\User;

class SavedCartFilter implements PageableInterface
{
    use Pageable;

    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
