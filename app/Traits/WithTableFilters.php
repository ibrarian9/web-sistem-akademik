<?php

namespace App\Traits;

use Livewire\WithPagination;

trait WithTableFilters
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'sortBy', 'sortDirection']);
        $this->resetPage();
    }

    public function applySorting($query)
    {
        return $query->orderBy($this->sortBy, $this->sortDirection);
    }
}
