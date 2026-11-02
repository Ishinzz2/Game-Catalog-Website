<?php
namespace App\Livewire\Admin;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;
class CategoryManager extends Component {
    use WithPagination;
    public string $search='';
    public ?int $editingId=null;
    public string $name='';
    public string $slug='';
    public ?string $description=null;
    public function mount():void { abort_unless(auth()->user()?->isAdmin(),403); }
    public function updatedName():void { if(!$this->editingId)$this->slug=Str::slug($this->name); }
    protected function rules():array { return [
        'name'=>['required','string','max:255'],
        'slug'=>['required','string','max:255',Rule::unique('categories','slug')->ignore($this->editingId)],
        'description'=>['nullable','string','max:2000'],
    ]; }
    public function save():void {
        $data=$this->validate();
        $category=$this->editingId?Category::findOrFail($this->editingId):new Category();
        $category->fill($data)->save();
        $this->resetForm();
        session()->flash('status','Kategori berhasil disimpan.');
    }
    public function edit(int $id):void {
        $category=Category::findOrFail($id);
        $this->editingId=$category->id;
        $this->name=$category->name;
        $this->slug=$category->slug;
        $this->description=$category->description;
    }
    public function delete(int $id):void {
        Category::findOrFail($id)->delete();
        session()->flash('status','Kategori berhasil dihapus. Relasi game pada kategori ini ikut dilepas.');
    }
    public function resetForm():void { $this->reset(['editingId','name','slug','description']); }
    public function render() {
        return view('livewire.admin.category-manager',[
            'categories'=>Category::query()->withCount('games')->when($this->search,fn($q)=>$q->where('name','like','%'.$this->search.'%'))->orderBy('name')->paginate(10)
        ]);
    }
}
