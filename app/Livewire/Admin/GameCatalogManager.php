<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Game;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class GameCatalogManager extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $editingId = null;
    public string $title = '';
    public string $slug = '';
    public ?string $description = null;
    public ?string $cover_url = null;
    public ?string $banner_url = null;
    public ?string $release_date = null;
    public ?string $metacritic_score = null;
    public ?string $esrb_rating = null;
    public array $categoryIds = [];

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function mount(): void
    {
        $this->authorizeAdmin();
    }

    public function updatedTitle(): void
    {
        $this->authorizeAdmin();

        if (! $this->editingId) {
            $this->slug = Str::slug($this->title);
        }
    }

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('games', 'slug')->ignore($this->editingId)],
            'description' => ['nullable', 'string'],
            'cover_url' => ['nullable', 'url', 'max:2048'],
            'banner_url' => ['nullable', 'url', 'max:2048'],
            'release_date' => ['nullable', 'date'],
            'metacritic_score' => ['nullable', 'integer', 'between:0,100'],
            'esrb_rating' => ['nullable', 'string', 'max:20'],
            'categoryIds' => ['array'],
            'categoryIds.*' => ['integer', 'exists:categories,id'],
        ];
    }

    public function save(): void
    {
        $this->authorizeAdmin();
        $data = $this->validate();

        $categoryIds = $data['categoryIds'] ?? [];
        unset($data['categoryIds']);

        $game = $this->editingId
            ? Game::findOrFail($this->editingId)
            : new Game();

        $game->fill($data)->save();
        $game->categories()->sync($categoryIds);
        $this->resetForm();

        session()->flash('status', 'Data game katalog berhasil disimpan.');
    }

    public function edit(int $id): void
    {
        $this->authorizeAdmin();
        $game = Game::findOrFail($id);

        $this->editingId = $game->id;

        foreach (['title', 'slug', 'description', 'cover_url', 'banner_url', 'esrb_rating'] as $field) {
            $this->$field = $game->$field;
        }

        $this->release_date = $game->release_date?->format('Y-m-d');
        $this->metacritic_score = $game->metacritic_score === null
            ? null
            : (string) $game->metacritic_score;
        $this->categoryIds = $game->categories()->pluck('categories.id')->map(fn ($id) => (int) $id)->all();
    }

    public function delete(int $id): void
    {
        $this->authorizeAdmin();

        Game::findOrFail($id)->delete();
        session()->flash('status', 'Game dihapus dari katalog.');
    }

    public function resetForm(): void
    {
        $this->authorizeAdmin();
        $this->reset([
            'editingId',
            'title',
            'slug',
            'description',
            'cover_url',
            'banner_url',
            'release_date',
            'metacritic_score',
            'esrb_rating',
            'categoryIds',
        ]);
    }

    public function render()
    {
        $this->authorizeAdmin();

        return view('livewire.admin.game-catalog-manager', [
            'categories' => Category::query()->orderBy('name')->get(['id', 'name']),
            'games' => Game::query()
                ->when($this->search, fn ($query) => $query->where('title', 'like', '%'.$this->search.'%'))
                ->orderBy('title')
                ->paginate(10),
        ]);
    }
}
