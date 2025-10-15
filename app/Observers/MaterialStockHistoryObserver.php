<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Material;
use App\Models\MaterialStockHistory;
use Illuminate\Support\Facades\Auth;

class MaterialStockHistoryObserver
{
    /**
     * Handle the MaterialStockHistory "created" event.
     */
    public function created(MaterialStockHistory $materialStockHistory): void
    {
        $this->updateMaterialStock($materialStockHistory);
    }

    /**
     * Handle the MaterialStockHistory "updated" event.
     */
    public function updated(MaterialStockHistory $materialStockHistory): void
    {
        // Jika material_id, type, atau quantity berubah, kita perlu update stock
        if ($materialStockHistory->wasChanged(['material_id', 'type', 'quantity'])) {
            // Kembalikan stock dari data lama
            if ($materialStockHistory->getOriginal('material_id')) {
                $this->revertMaterialStock($materialStockHistory);
            }

            // Terapkan stock dengan data baru
            $this->updateMaterialStock($materialStockHistory);
        }
    }

    /**
     * Handle the MaterialStockHistory "deleted" event.
     */
    public function deleted(MaterialStockHistory $materialStockHistory): void
    {
        $this->revertMaterialStock($materialStockHistory);
    }

    /**
     * Update stock material berdasarkan pergerakan stock
     */
    private function updateMaterialStock(MaterialStockHistory $materialStockHistory): void
    {
        $material = Material::find($materialStockHistory->material_id);

        if (!$material) {
            return;
        }

        // Update current_stock berdasarkan tipe pergerakan
        if ($materialStockHistory->type === 'in') {
            $material->current_stock += $materialStockHistory->quantity;
        } else {
            $material->current_stock -= $materialStockHistory->quantity;
        }

        // Update last_updated_by dengan user yang membuat history ini
        $material->last_updated_by = $materialStockHistory->created_by;

        $material->save();
    }

    /**
     * Kembalikan stock material ke kondisi sebelum pergerakan (untuk update/delete)
     */
    private function revertMaterialStock(MaterialStockHistory $materialStockHistory): void
    {
        $materialId = $materialStockHistory->getOriginal('material_id') ?? $materialStockHistory->material_id;
        $material = Material::find($materialId);

        if (!$material) {
            return;
        }

        $originalType = $materialStockHistory->getOriginal('type') ?? $materialStockHistory->type;
        $originalQuantity = $materialStockHistory->getOriginal('quantity') ?? $materialStockHistory->quantity;

        // Kembalikan stock dengan operasi kebalikan
        if ($originalType === 'in') {
            $material->current_stock -= $originalQuantity;
        } else {
            $material->current_stock += $originalQuantity;
        }

        $material->last_updated_by = Auth::id();

        $material->save();
    }
}
