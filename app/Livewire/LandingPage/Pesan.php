<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Transaction;
use App\Services\InvoiceService;
use App\Services\OrderRateLimiterService;
use Carbon\Carbon;
use Livewire\Component;
use Mary\Traits\Toast;
use Illuminate\Support\Collection;

class Pesan extends Component
{
    use Toast;

    // Customer data
    public string $name = '';
    public string $phone = '';
    public string $email = '';
    public string $address = '';

    // Transaction data
    public ?int $service_id = null;
    public string $payment_timing = 'on_delivery';
    public string $notes = '';

    // Security & tracking
    public int $form_loaded_at = 0;
    public string $honeypot = ''; // Honeypot field untuk detect bot

    // Services list
    public Collection $services;

    public function mount(): void
    {
        // Load active services
        $this->services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Set form loaded timestamp untuk bot detection
        $this->form_loaded_at = now()->timestamp;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'phone' => ['required', 'string', 'regex:/^8[0-9]{8,11}$/', 'min:9', 'max:13'],
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|min:10|max:500',
            'service_id' => 'required|exists:services,id',
            'payment_timing' => 'required|in:on_pickup,on_delivery',
            'notes' => 'nullable|string|max:1000',
            'honeypot' => 'size:0', // Honeypot harus kosong
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid. Contoh: 081234567890',
            'email.email' => 'Format email tidak valid.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.min' => 'Alamat minimal 10 karakter.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'service_id.required' => 'Silakan pilih layanan.',
            'service_id.exists' => 'Layanan tidak valid.',
            'payment_timing.required' => 'Silakan pilih waktu pembayaran.',
            'payment_timing.in' => 'Waktu pembayaran tidak valid.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
            'honeypot.size' => 'Form submission tidak valid.',
        ];
    }

    public function save(): void
    {
        // Validate form
        $this->validate();

        // Check honeypot (bot detection)
        if (!empty($this->honeypot)) {
            $this->error(
                'Submission tidak valid',
                'Silakan refresh halaman dan coba lagi.',
                position: 'toast-top toast-end',
                timeout: 5000
            );
            return;
        }

        // Rate limiting check
        $rateLimiter = app(OrderRateLimiterService::class);

        $rateLimitResult = $rateLimiter->checkAllLimits(
            request()->ip() ?? '127.0.0.1',
            $this->phone,
            $this->form_loaded_at
        );

        if (!$rateLimitResult['passed']) {
            $this->warning(
                'Terlalu cepat!',
                $rateLimitResult['errors'][0],
                position: 'toast-top toast-end',
                timeout: 5000
            );
            return;
        }

        try {
            // Find or create customer by phone number
            $customer = Customer::firstOrCreate(
                ['phone' => $this->phone],
                [
                    'name' => $this->name,
                    'email' => $this->email,
                    'address' => $this->address,
                    'member' => false,
                ]
            );

            // Jika customer sudah ada, update address dan email (jika sebelumnya kosong)
            // Name tetap pakai data yang pertama kali didaftarkan
            if (!$customer->wasRecentlyCreated) {
                $updateData = [
                    'address' => $this->address,
                ];

                // Update email hanya jika sebelumnya kosong dan sekarang diisi
                if (empty($customer->email) && !empty($this->email)) {
                    $updateData['email'] = $this->email;
                }

                $customer->update($updateData);
            }

            // Get selected service
            $service = Service::findOrFail($this->service_id);

            // Calculate estimated finish date
            $estimatedFinishDate = now()->addDays($service->duration_days);

            // Create transaction
            $transaction = Transaction::create([
                'invoice_number' => app(InvoiceService::class)->generateInvoiceNumber(),
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'courier_motorcycle_id' => null, // Akan diassign oleh admin
                'pos_id' => null, // Akan diassign oleh admin
                'weight' => 0, // Akan ditimbang oleh kurir
                'price_per_kg' => $service->price_per_kg,
                'total_price' => 0, // Akan dihitung setelah ditimbang
                'workflow_status' => 'pending_confirmation',
                'payment_timing' => $this->payment_timing,
                'payment_status' => 'unpaid',
                'payment_proof_url' => null,
                'paid_at' => null,
                'notes' => $this->notes,
                'order_date' => now(),
                'estimated_finish_date' => $estimatedFinishDate,
                'actual_finish_date' => null,
                'form_loaded_at' => Carbon::createFromTimestamp($this->form_loaded_at),
                // tracking_token, customer_ip, customer_user_agent akan di-set oleh Observer
            ]);

            // Success toast
            $this->success(
                'Pesanan Berhasil Dibuat! 🎉',
                "Invoice: {$transaction->invoice_number}. Tim kami akan segera menghubungi Anda via WhatsApp.",
                position: 'toast-top toast-end',
                timeout: 8000
            );

            // Reset form
            $this->reset([
                'name',
                'phone',
                'email',
                'address',
                'service_id',
                'notes',
                'honeypot'
            ]);

            // Set back default values
            $this->payment_timing = 'on_delivery';
            $this->form_loaded_at = now()->timestamp;

            // Optional: Redirect ke halaman tracking atau beranda
            // $this->redirect(route('tracking', ['token' => $transaction->tracking_token]));

        } catch (\Exception $e) {
            // Error handling
            $this->error(
                'Terjadi Kesalahan',
                'Maaf, pesanan tidak dapat diproses. Silakan coba lagi atau hubungi CS kami.',
                position: 'toast-top toast-end',
                timeout: 5000
            );

            // Log error for debugging
            logger()->error('Order creation failed', [
                'error' => $e->getMessage(),
                'phone' => $this->phone,
                'service_id' => $this->service_id,
            ]);
        }
    }

    public function resetForm(): void
    {
        // Reset form fields
        $this->reset([
            'name',
            'phone',
            'email',
            'address',
            'service_id',
            'notes',
            'honeypot'
        ]);

        // Set back default values
        $this->payment_timing = 'on_delivery';
        $this->form_loaded_at = now()->timestamp;

        // Clear all validation errors
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.landing-page.pesan');
    }
}
