<?php

namespace App\Livewire\ApprovalFlow;

use App\Livewire\Traits\HasNotification;
use App\Models\ApprovalFlow;
use App\Services\ApprovalFlowService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithPagination;

class ApprovalFlowManagement extends Component
{
    use HasNotification, WithPagination;

    public $search = '';

    public $showDeleteModal = false;

    public $deleteId;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function duplicate(int $id, ApprovalFlowService $service): void
    {
        try {
            $this->authorize('create', ApprovalFlow::class);
            $copy = $service->duplicate(ApprovalFlow::findOrFail($id));
            $this->notifySuccess('Alur berhasil disalin. Sesuaikan nama dan aktifkan jika perlu.');
            $this->redirect(route('approval-flows.edit', $copy), navigate: true);
        } catch (AuthorizationException) {
            $this->notifyError('Anda tidak memiliki izin untuk membuat alur.');
        } catch (\Exception $e) {
            report($e);
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(ApprovalFlowService $service): void
    {
        try {
            $flow = ApprovalFlow::findOrFail($this->deleteId);
            $this->authorize('delete', $flow);
            $service->delete($flow);
            $this->notifySuccess('Alur persetujuan berhasil dihapus.');
        } catch (AuthorizationException) {
            $this->notifyError('Anda tidak memiliki izin untuk menghapus alur ini.');
        } catch (ValidationException $e) {
            $this->notifyValidationError($e);
        } catch (\Exception $e) {
            report($e);
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }

        $this->reset(['showDeleteModal', 'deleteId']);
    }

    public function toggleActive(int $id): void
    {
        try {
            $flow = ApprovalFlow::findOrFail($id);
            $this->authorize('update', $flow);
            $flow->update(['is_active' => ! $flow->is_active]);
            $this->notifySuccess($flow->is_active ? 'Alur diaktifkan.' : 'Alur dinonaktifkan.');
        } catch (AuthorizationException) {
            $this->notifyError('Anda tidak memiliki izin untuk mengubah alur ini.');
        } catch (\Exception $e) {
            report($e);
            $this->notifyError('Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function render(ApprovalFlowService $service)
    {
        return view('livewire.approval-flow.approval-flow-management', [
            'flows' => $service->getFiltered(['search' => $this->search]),
        ]);
    }
}
