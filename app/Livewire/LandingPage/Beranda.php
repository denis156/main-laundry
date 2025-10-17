<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\Pos;
use Livewire\Component;

class Beranda extends Component
{
    public function render()
    {
        // Get real stats from database
        $totalCustomers = Customer::count();
        $totalTransactions = Transaction::count();
        $totalPos = Pos::where('is_active', true)->count();

        return view('livewire.landing-page.beranda', [
            'totalCustomers' => $totalCustomers,
            'totalTransactions' => $totalTransactions,
            'totalPos' => $totalPos,
        ]);
    }
}
