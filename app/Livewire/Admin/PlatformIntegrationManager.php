<?php
namespace App\Livewire\Admin;
use App\Models\PlatformIntegration;
use Illuminate\Validation\Rule;
use Livewire\Component;
class PlatformIntegrationManager extends Component {
 public ?int $editingId=null;
 public string $provider_type='steam';
 public string $name='';
 public ?string $api_base_url=null;
 public ?string $api_key=null;
 public bool $is_enabled=false;
 public ?string $notes=null;
 public function rules():array{return [
  'provider_type'=>['required',Rule::in(['steam','epic','playstation','xbox','nintendo','gog','custom'])],
  'name'=>['required','string','max:255'],
  'api_base_url'=>['nullable','url','max:2048'],
  'api_key'=>['nullable','string','max:4096'],
  'is_enabled'=>['boolean'],
  'notes'=>['nullable','string','max:2000'],
 ];}
 public function save():void{
  $data=$this->validate();
  $item=$this->editingId?PlatformIntegration::findOrFail($this->editingId):new PlatformIntegration();
  if($this->editingId && blank($data['api_key']))unset($data['api_key']);
  if(blank($data['api_key']??null))unset($data['api_key']);
  $item->fill($data)->save();
  $this->resetForm();
  session()->flash('status','Konfigurasi integrasi berhasil disimpan. API key disimpan terenkripsi.');
 }
 public function edit(int $id):void{
  $item=PlatformIntegration::findOrFail($id);
  $this->editingId=$item->id;$this->provider_type=$item->provider_type;$this->name=$item->name;
  $this->api_base_url=$item->api_base_url;$this->api_key=null;$this->is_enabled=$item->is_enabled;$this->notes=$item->notes;
 }
 public function delete(int $id):void{PlatformIntegration::findOrFail($id)->delete();session()->flash('status','Konfigurasi integrasi dihapus.');}
 public function resetForm():void{$this->reset(['editingId','name','api_base_url','api_key','notes']);$this->provider_type='steam';$this->is_enabled=false;}
 public function render(){return view('livewire.admin.platform-integration-manager',['integrations'=>PlatformIntegration::latest()->get()]);}
}