<?php
namespace App\Livewire;
use App\Models\Platform;
use App\Models\UserPlatformAccount;
use Illuminate\Validation\Rule;
use Livewire\Component;
class PlatformAccountManager extends Component {
    public ?int $platform_id=null;
    public ?string $account_username=null;
    public ?string $account_id_external=null;
    public ?int $editingId=null;
    protected function rules():array{return [
        'platform_id'=>['required','integer',Rule::exists('platforms','id')],
        'account_username'=>['nullable','string','max:255'],
        'account_id_external'=>['nullable','string','max:255'],
    ];}
    public function save():void {
        $data=$this->validate();
        $account=$this->editingId
            ? UserPlatformAccount::where('user_id',auth()->id())->findOrFail($this->editingId)
            : new UserPlatformAccount(['user_id'=>auth()->id()]);
        $account->fill($data)->save();
        $this->reset(['platform_id','account_username','account_id_external','editingId']);
        session()->flash('status','Akun platform berhasil disimpan.');
    }
    public function edit(int $id):void {
        $item=UserPlatformAccount::where('user_id',auth()->id())->findOrFail($id);
        $this->editingId=$item->id;$this->platform_id=$item->platform_id;
        $this->account_username=$item->account_username;$this->account_id_external=$item->account_id_external;
    }
    public function delete(int $id):void {
        UserPlatformAccount::where('user_id',auth()->id())->findOrFail($id)->delete();
        session()->flash('status','Akun platform dihapus. Inventaris terkait mengikuti aturan relasi database.');
    }
    public function render(){return view('livewire.platform-account-manager',[
        'platforms'=>Platform::orderBy('name')->get(),
        'accounts'=>UserPlatformAccount::where('user_id',auth()->id())->with('platform')->latest()->get()
    ]);}
}