<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RefTypePaiement;

class TypePaiementManager extends Component
{
    use WithPagination;

    public $type_id = null;
    public $LibPaie = '';
    public $editMode = false;

    protected $rules = [
        'LibPaie' => 'required|string|max:255',
    ];

    public function updating($property)
    {
        if ($property === 'LibPaie') {
            $this->resetPage();
        }
    }

    public function resetForm()
    {
        $this->type_id = null;
        $this->LibPaie = '';
        $this->editMode = false;
    }

    public function saveTypePaiement()
    {
        $this->validate();
        if ($this->editMode && $this->type_id) {
            $type = RefTypePaiement::find($this->type_id);
            if ($type) {
                $type->update([
                    'LibPaie' => $this->LibPaie,
                ]);
            }
        } else {
            RefTypePaiement::create([
                'LibPaie' => $this->LibPaie,
            ]);
        }
        $this->resetForm();
        $this->resetPage();
    }

    public function editTypePaiement($id)
    {
        $type = RefTypePaiement::find($id);
        if ($type) {
            $this->type_id = $type->idtypepaie;
            $this->LibPaie = $type->LibPaie;
            $this->editMode = true;
        }
    }

    public function deleteTypePaiement($id)
    {
        $type = RefTypePaiement::find($id);
        if ($type) {
            $type->delete();
            $this->resetForm();
            $this->resetPage();
        }
    }

    public function render()
    {
        $types = RefTypePaiement::orderByDesc('idtypepaie')->paginate(5);
        return view('livewire.type-paiement-manager', compact('types'));
    }
}
