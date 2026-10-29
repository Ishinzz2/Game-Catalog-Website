<?php
namespace App\Livewire;
use App\Models\Game;
use App\Models\Platform;
use App\Models\Wishlist;
use Illuminate\Validation\Rule;
use Livewire\Component;
class WishlistManager extends Component {
 public ?int $game_id=null;
 public ?int $target_platform_id=null;
 public ?string $desired_max_price=null;
 public string $priority='Medium';
 public ?string $notes=null;
 protected function rules():array{return ['game_id'=>['required','integer',Rule::exists('games','id')],'target_platform_id'=>['nullable','integer',Rule::exists('platforms','id')],'desired_max_price'=>['nullable','numeric','min:0','max:9999999999.99'],'priority'=>['required',Rule::in(['Low','Medium','High'])],'notes'=>['nullable','string','max:2000']];}
 public function save():void{$data=$this->validate();Wishlist::create(['user_id'=>auth()->id(),...$data]);$this->reset(['game_id','target_platform_id','desired_max_price','notes']);$this->priority='Medium';session()->flash('status','Game berhasil ditambahkan ke wishlist.');}
 public function delete(int $id):void{Wishlist::where('user_id',auth()->id())->findOrFail($id)->delete();session()->flash('status','Wishlist dihapus.');}
 public function render(){return view('livewire.wishlist-manager',['games'=>Game::orderBy('title')->get(['id','title']),'platforms'=>Platform::orderBy('name')->get(),'items'=>Wishlist::where('user_id',auth()->id())->with(['game','targetPlatform'])->latest()->get()]);}
}