<?php

namespace App\Livewire\MasterData;

use App\Enums\BisnisAreaStatus;
use App\Livewire\Traits\HasNotification;
use App\Livewire\Traits\WithSorting;
use App\Models\BisnisArea;
use App\Services\BisnisAreaService;
use App\Traits\HasDynamicLike;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class BisnisAreaManagement extends Component
{
    use AuthorizesRequests, HasDynamicLike, HasNotification, WithPagination, WithSorting;

    public $search = '';

    public $statusFilter = '';

    public bool $filterChanged = false;

    public $showModal = false;

    public $editMode = false;

    public $bisnisAreaId;

    public $name;

    public $description;

    public $status = 'aktif';

    public $showDeleteModal = false;

    public $deletingBisnisAreaId;

    public $deletingBisnisAreaName;

    public function mount()
    {
        $this->authorize('viewAny', BisnisArea::class);
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255', $this->editMode ? 'unique:bisnis_areas,name,'.$this->bisnisAreaId : 'unique:bisnis_areas,name'],
            'description' => 'nullable|string|max:1000',
            'status' => ['required', 'string', 'in:'.implode(',', BisnisAreaStatus::values())],
        ];
    }

    public function validationAttributes()
    {
        return [
            'name' => 'nama bisnis area',
            'description' => 'deskripsi',
            'status' => 'status',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
        $this->filterChanged = true;
    }

    public function resetFilters()
    {
        $this->statusFilter = '';
        $this->resetPage();
        $this->filterChanged = true;
        $this->notifySuccess('Filter berhasil direset.');
    }

    public function getStatusOptionsProperty(): array
    {
        return collect(BisnisAreaStatus::cases())->map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ])->toArray();
    }

    public function create()
    {
        $this->authorize('create', BisnisArea::class);
        $this->resetForm();
        $this->editMode = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $bisnisArea = BisnisArea::findOrFail($id);
        $this->authorize('update', $bisnisArea);

        $this->bisnisAreaId = $bisnisArea->id;
        $this->name = $bisnisArea->name;
        $this->description = $bisnisArea->description;
        $this->status = $bisnisArea->status->value;

        $this->editMode = true;
        $this->showModal = true;
    }

    public function save(BisnisAreaService $service)
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->notifyValidationError($e);
            throw $e;
        }

        try {
            $data = [
                'name' => $this->name,
                'description' => $this->description,
                'status' => $this->status,
            ];

            if ($this->editMode) {
                $bisnisArea = BisnisArea::findOrFail($this->bisnisAreaId);
                $this->authorize('update', $bisnisArea);
                $service->update($bisnisArea, $data);
                $message = 'Bisnis Area berhasil diupdate!';
            } else {
                $this->authorize('create', BisnisArea::class);
                $service->create($data);
                $message = 'Bisnis Area berhasil ditambahkan!';
            }

            $this->notifySuccess($message);
            $this->closeModal();
        } catch (AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function confirmDelete($id)
    {
        $bisnisArea = BisnisArea::findOrFail($id);
        $this->deletingBisnisAreaId = $bisnisArea->id;
        $this->deletingBisnisAreaName = $bisnisArea->name;
        $this->showDeleteModal = true;
    }

    public function delete(BisnisAreaService $service)
    {
        try {
            $bisnisArea = BisnisArea::findOrFail($this->deletingBisnisAreaId);
            $this->authorize('delete', $bisnisArea);

            $service->delete($bisnisArea);
            $this->notifySuccess('Bisnis Area berhasil dihapus!');
            $this->showDeleteModal = false;
        } catch (AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
            $this->showDeleteModal = false;
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
            $this->showDeleteModal = false;
        }
    }

    public function toggleStatus(BisnisAreaService $service, $id)
    {
        try {
            $bisnisArea = BisnisArea::findOrFail($id);
            $this->authorize('toggleStatus', $bisnisArea);

            $service->toggleStatus($bisnisArea);
            $this->notifySuccess('Status bisnis area berhasil diubah!');
        } catch (AuthorizationException $e) {
            $this->notifyError('Anda tidak memiliki izin untuk melakukan aksi ini.');
        } catch (\Exception $e) {
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    private function resetForm()
    {
        $this->bisnisAreaId = null;
        $this->name = '';
        $this->description = '';
        $this->status = 'aktif';
    }

    public function render(BisnisAreaService $service)
    {
        $bisnisAreas = $service->getFiltered($this->search, $this->statusFilter, sortField: $this->sortField, sortDirection: $this->sortDirection);

        return view('livewire.master-data.bisnis-area-management', compact('bisnisAreas'));
    }
}
