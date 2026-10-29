<?php
namespace App\\Livewire;
use App\\Models\\Game;
use App\\Models\\GamePlaySession;
use App\\Models\\UserGameInventory;
use App\\Models\\UserPlatformAccount;
use Illuminate\\Support\\Facades\\DB;
use Illuminate\\Validation\\Rule;
use Livewire\\Component;
use Livewire\\WithPagination;
class InventoryManager extends Component {
    use WithPagination;
    public string $search = '';
    public ?int $game_id = null;
    public ?int $user_platform_account_id = null;
    public string $format = 'Digital';
    public string $acquisition_type = 'Purchased';
    public ?string $purchase_price = null;
    public ?string $purchase_date = null;
    public string $completion_status = 'Backlog';
    public int $playtime_minutes = 0;
    public bool $is_installed = false;
    public ?int $editingId = null;
    protected function rules(): array {
        return [
            'game_id'=>['required','integer',Rule::exists('games','id')],
            'user_platform_account_id'=>['required','integer',Rule::exists('user_platform_accounts','id')->where('user_id',auth()->id())],
            'format'=>['required',Rule::in(['Digital','Physical Disc','Physical Cartridge'])],
            'acquisition_type'=>['required',Rule::in(['Purchased','Subscription/Game Pass/PS Plus','Gift','Free-to-Play'])],
            'purchase_price'=>['nullable','numeric','min:0','max:9999999999.99'],
            'purchase_date'=>['nullable','date','before_or_equal:today'],
            'completion_status'=>['required',Rule::in(['Unplayed','Backlog','Playing','Completed','100% Platinum','Dropped'])],
            'playtime_minutes'=>['required','integer','min:0','max:10000000'],
            'is_installed'=>['boolean']
        ];
    }
    public function save(): void {
        $data=$this->validate();
        $user=auth()->user();
        $account=UserPlatformAccount::where('user_id',$user->id)->findOrFail($data['user_platform_account_id']);
        DB::transaction(function() use($data,$user,$account) {
            $inventory= $this->editingId
                ? UserGameInventory::where('user_id',$user->id)->findOrFail($this->editingId)
                : new UserGameInventory();
            $inventory->fill([
                'user_id'=>$user->id,'game_id'=>$data['game_id'],
                'user_platform_account_id'=>$account->id,'format'=>$data['format'],
                'acquisition_type'=>$data['acquisition_type'],'purchase_price'=>$data['purchase_price'] ?? null,
                'purchase_date'=>$data['purchase_date'] ?? null,'is_installed'=>$data['is_installed']
            ])->save();
            GamePlaySession::updateOrCreate(['user_game_inventory_id'=>$inventory->id],[
                'completion_status'=>$data['completion_status'],'playtime_minutes'=>$data['playtime_minutes']
            ]);
        });
        $this->reset(['game_id','user_platform_account_id','purchase_price','purchase_date','editingId','playtime_minutes','is_installed']);
        $this->format='Digital'; $this->acquisition_type='Purchased'; $this->completion_status='Backlog';
        session()->flash('status','Inventaris berhasil disimpan.');
    }
    public function edit(int $id): void {
        $item=UserGameInventory::where('user_id',auth()->id())->with('playSession')->findOrFail($id);
        $this->editingId=$item->id; $this->game_id=$item->game_id;
        $this->user_platform_account_id=$item->user_platform_account_id;
        $this->format=$item->format; $this->acquisition_type=$item->acquisition_type;
        $this->purchase_price=$item->purchase_price; $this->purchase_date=$item->purchase_date?->format('Y-m-d');
        $this->is_installed=$item->is_installed;
        $this->completion_status=$item->playSession?->completion_status ?? 'Backlog';
        $this->playtime_minutes=$item->playSession?->playtime_minutes ?? 0;
    }
    public function delete(int $id): void {
        UserGameInventory::where('user_id',auth()->id())->findOrFail($id)->delete();
        session()->flash('status','Item inventaris dihapus.');
    }
    public function render() {
        return view('livewire.inventory-manager',[
            'games'=>Game::orderBy('title')->get(['id','title']),
            'accounts'=>UserPlatformAccount::where('user_id',auth()->id())->with('platform')->get(),
            'inventories'=>UserGameInventory::where('user_id',auth()->id())
                ->with(['game','platformAccount.platform','playSession'])
                ->when($this->search,fn($q)=>$q->whereHas('game',fn($g)=>$g->where('title','like','%'.$this->search.'%')))
                ->latest()->paginate(8)
        ]);
    }
}
