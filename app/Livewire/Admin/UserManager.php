<?php
namespace App\Livewire\Admin;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
class UserManager extends Component {
    use WithPagination;
    public string $search='';
    private function authorizeAdmin():void { abort_unless(auth()->user()?->isAdmin(),403); }
    public function mount():void { $this->authorizeAdmin(); }
    public function updateRole(int $id,string $role):void {
        $this->authorizeAdmin();
        abort_unless(in_array($role,['admin','user'],true),422);
        $user=User::findOrFail($id);
        if($user->is(auth()->user())) {
            session()->flash('status','Role akun yang sedang digunakan tidak dapat diubah dari halaman ini.');
            return;
        }
        if($user->role==='admin' && $role==='user' && User::where('role','admin')->count()<=1) {
            session()->flash('status','Role tidak dapat diturunkan karena akun ini adalah administrator aktif terakhir.');
            return;
        }
        $user->role=$role;
        $user->save();
        session()->flash('status','Role pengguna berhasil diperbarui.');
    }
    public function delete(int $id):void {
        $this->authorizeAdmin();
        $user=User::findOrFail($id);
        if($user->is(auth()->user())) {
            session()->flash('status','Anda tidak dapat menghapus akun yang sedang digunakan.');
            return;
        }
        if($user->role==='admin' && User::where('role','admin')->count()<=1) {
            session()->flash('status','Administrator aktif terakhir tidak dapat dihapus.');
            return;
        }
        $user->delete();
        session()->flash('status','Akun pengguna berhasil dinonaktifkan (soft delete).');
    }
    public function render() {
        $this->authorizeAdmin();
        return view('livewire.admin.user-manager',[
            'users'=>User::query()->when($this->search,function($q){$term='%'.$this->search.'%';$q->where(fn($sub)=>$sub->where('name','like',$term)->orWhere('email','like',$term));})->orderByDesc('created_at')->paginate(12)
        ]);
    }
}
