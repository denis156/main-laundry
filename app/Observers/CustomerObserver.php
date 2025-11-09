<?php

declare(strict_types=1);

namespace App\Observers;

use App\Helper\Database\CustomerHelper;
use App\Helper\WilayahHelper;
use App\Models\Customer;

/**
 * Customer Observer
 *
 * Observer ini tidak lagi digunakan karena data customer sekarang disimpan di JSONB.
 * Address handling dilakukan di CustomerHelper, bukan di Observer.
 *
 * File ini di-keep untuk backward compatibility, tapi tidak melakukan apa-apa.
 */
class CustomerObserver
{
    public function creating(Customer $customer): void
    {
        // Data sekarang di JSONB, tidak perlu auto-sync
        // TODO: Remove this observer after migration selesai
    }

    public function updating(Customer $customer): void
    {
        // Data sekarang di JSONB, tidak perlu auto-sync
        // TODO: Remove this observer after migration selesai
    }
}
