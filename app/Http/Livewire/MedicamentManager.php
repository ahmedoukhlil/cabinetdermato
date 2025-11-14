<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Medicament;

class MedicamentManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedType = '';
    public $perPage = 10;

    // Propriétés pour le formulaire
    public $medicamentId;
    public $libelleMedic;
    public $fkidtype;
    public $prixRef = 0;

    // Modals
    public $showModal = false;
    public $showDeleteModal = false;
    public $medicamentToDelete;

    protected function rules()
    {
        return [
            'libelleMedic' => 'required|min:3',
            'fkidtype' => 'required|integer|in:1,2,3', // 1 = Médicament, 2 = Analyse, 3 = Radio
            'prixRef' => 'nullable|numeric|min:0',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function getTypesProperty()
    {
        // Types : 1 = Médicament, 2 = Analyse, 3 = Radio
        return [
            ['id' => 1, 'Type' => 'Médicament'],
            ['id' => 2, 'Type' => 'Analyse'],
            ['id' => 3, 'Type' => 'Radio'],
        ];
    }

    public function openModal($id = null)
    {
        $this->resetForm();
        if ($id) {
            $medicament = Medicament::find($id);
            if ($medicament) {
                $this->medicamentId = $medicament->IDMedic;
                $this->libelleMedic = $medicament->LibelleMedic;
                $this->fkidtype = $medicament->fkidtype;
                $this->prixRef = $medicament->PrixRef ?? 0;
            }
        }
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();
        $data = [
            'LibelleMedic' => $this->libelleMedic,
            'fkidtype' => $this->fkidtype,
            'PrixRef' => $this->prixRef ?? 0,
        ];
        if ($this->medicamentId) {
            $medicament = Medicament::find($this->medicamentId);
            if ($medicament) {
                $medicament->update($data);
                session()->flash('message', 'Médicament modifié avec succès.');
            }
        } else {
            Medicament::create($data);
            session()->flash('message', 'Médicament créé avec succès.');
        }
        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->medicamentToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteMedicament()
    {
        $medicament = Medicament::find($this->medicamentToDelete);
        if ($medicament) {
            $medicament->delete();
            session()->flash('message', 'Médicament supprimé avec succès.');
        }
        $this->showDeleteModal = false;
        $this->medicamentToDelete = null;
    }

    public function resetForm()
    {
        $this->medicamentId = null;
        $this->libelleMedic = '';
        $this->fkidtype = '';
        $this->prixRef = 0;
    }

    public function render()
    {
        $query = Medicament::orderBy('LibelleMedic');

        if ($this->search) {
            $query->where('LibelleMedic', 'like', '%' . $this->search . '%');
        }
        if ($this->selectedType) {
            $query->where('fkidtype', $this->selectedType);
        }

        $medicaments = $query->paginate($this->perPage);

        return view('livewire.medicament-manager', [
            'medicaments' => $medicaments,
            'types' => $this->getTypesProperty(),
        ]);
    }
}
