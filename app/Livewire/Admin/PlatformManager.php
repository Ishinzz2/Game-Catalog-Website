<?php

namespace App\Livewire\Admin;

use App\Models\Platform;
use App\Models\UserPlatformAccount;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class PlatformManager extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $editingId = null;
    public string $name = '';
    public string $code = '';
    public ?string $company_name = null;
    public ?string $icon_path = null;

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }

    public function mount(): void
    {
        $this->authorizeAdmin();
    }

    public function updatedName(): void
    {
        $this->authorizeAdmin();

        if (! $this->editingId) {
            $this->code = Str::slug($this->name);
        }
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'alpha_dash', 'max:100', Rule::unique('platforms', 'code')->ignore($this->editingId)],
            'company_name' => ['nullable', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:2048'],
        ];
    }

    public function save(): void
    {
        $this->authorizeAdmin();
        $data = $this->validate();

        $platform = $this->editingId
            ? Platform::findOrFail($this->editingId)
            : new Platform();

        $platform->fill($data)->save();
        $this->resetForm();

        session()->flash('status', 'Platform berhasil disimpan.');
    }

    public function edit(int $id): void
    {
        $this->authorizeAdmin();
        $platform = Platform::findOrFail($id);

        $this->editingId = $platform->id;
        $this->name = $platform->name;
        $this->code = $platform->code;
        $this->company_name = $platform->company_name;
        $this->icon_path = $platform->icon_path;
    }

    public function delete(int $id): void
    {
        $this->authorizeAdmin();
        $platform = Platform::findOrFail($id);

        if (UserPlatformAccount::withTrashed()->where('platform_id', $platform->id)->exists()) {
            session()->flash('status', 'Platform tidak dapat dihapus karena masih digunakan oleh akun pengguna. Edit datanya atau hapus relasi akun terkait terlebih dahulu.');

            return;
        }

        $platform->delete();
        session()->flash('status', 'Platform berhasil dihapus.');
    }

    public function resetForm(): void
    {
        $this->authorizeAdmin();
        $this->reset(['editingId', 'name', 'code', 'company_name', 'icon_path']);
    }

    public function render()
    {
        $this->authorizeAdmin();

        return view('livewire.admin.platform-manager', [
            'platforms' => Platform::query()
                ->withCount('userAccounts')
                ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
