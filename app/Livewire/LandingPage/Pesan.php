<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Transaction;
use App\Services\InvoiceService;
use App\Services\OrderRateLimiterService;
use App\Services\WilayahService;
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
    public string $detail_address = ''; // Detail alamat (Jl, RT/RW, dll)
    public string $district_code = ''; // Kode kecamatan
    public string $village_code = ''; // Kode kelurahan

    // Transaction data
    public ?int $service_id = null;
    public string $payment_timing = 'on_delivery';
    public string $notes = '';

    // Security & tracking
    public int $form_loaded_at = 0;
    public string $honeypot = ''; // Honeypot field untuk detect bot

    // Services list
    public Collection $services;

    // Wilayah list
    public array $districts = []; // List kecamatan di Kota Kendari
    public array $villages = []; // List kelurahan berdasarkan kecamatan

    public function mount(WilayahService $wilayahService): void
    {
        // Load active services
        $this->services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Load districts (kecamatan) di Kota Kendari
        $this->districts = $wilayahService->getKendariDistricts();

        // Set form loaded timestamp untuk bot detection
        $this->form_loaded_at = now()->timestamp;
    }

    /**
     * Format phone number untuk database
     * Hilangkan "0" di depan jika dimulai dengan "0"
     */
    private function formatPhoneForDatabase(string $phone): string
    {
        // Hilangkan semua karakter non-numeric
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Jika dimulai dengan "0", hilangkan
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = substr($cleanPhone, 1);
        }

        return $cleanPhone;
    }

    /**
     * Load villages saat user pilih kecamatan
     */
    public function updatedDistrictCode(WilayahService $wilayahService): void
    {
        // Reset village code
        $this->village_code = '';

        // Load villages berdasarkan district yang dipilih
        if (!empty($this->district_code)) {
            $this->villages = $wilayahService->getVillagesByDistrict($this->district_code);
        } else {
            $this->villages = [];
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'phone' => ['required', 'string', 'regex:/^8[0-9]{8,11}$/', 'min:9', 'max:13'],
            'email' => 'nullable|email|max:255',
            'detail_address' => 'required|string|min:10|max:500',
            'district_code' => 'required|string',
            'village_code' => 'required|string',
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
            'detail_address.required' => 'Detail alamat wajib diisi.',
            'detail_address.min' => 'Detail alamat minimal 10 karakter.',
            'detail_address.max' => 'Detail alamat maksimal 500 karakter.',
            'district_code.required' => 'Silakan pilih kecamatan.',
            'village_code.required' => 'Silakan pilih kelurahan.',
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
        // Format phone number untuk database dan validasi
        $this->phone = $this->formatPhoneForDatabase($this->phone);

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

        // Rate limiting check - hanya aktif di production
        if (app()->environment('production')) {
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
        }

        try {
            // Get district and village names from codes
            $wilayahService = app(WilayahService::class);

            $district = collect($this->districts)->firstWhere('code', $this->district_code);
            $village = collect($this->villages)->firstWhere('code', $this->village_code);

            $districtName = $district['name'] ?? '';
            $villageName = $village['name'] ?? '';

            // Format full address
            $fullAddress = $wilayahService->formatFullAddress(
                $this->detail_address,
                $villageName,
                $districtName
            );

            // Find or create customer by phone number
            $customer = Customer::firstOrCreate(
                ['phone' => $this->phone],
                [
                    'name' => $this->name,
                    'email' => $this->email,
                    'district_code' => $this->district_code,
                    'district_name' => $districtName,
                    'village_code' => $this->village_code,
                    'village_name' => $villageName,
                    'detail_address' => $this->detail_address,
                    'address' => $fullAddress,
                    'member' => false,
                ]
            );

            // Jika customer sudah ada, update wilayah, address dan email (jika sebelumnya kosong)
            // Name tetap pakai data yang pertama kali didaftarkan
            if (!$customer->wasRecentlyCreated) {
                $updateData = [
                    'district_code' => $this->district_code,
                    'district_name' => $districtName,
                    'village_code' => $this->village_code,
                    'village_name' => $villageName,
                    'detail_address' => $this->detail_address,
                    'address' => $fullAddress,
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
            // Event akan otomatis di-broadcast via TransactionObserver
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
                'detail_address',
                'district_code',
                'village_code',
                'service_id',
                'notes',
                'honeypot'
            ]);

            // Reset villages
            $this->villages = [];

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
            'detail_address',
            'district_code',
            'village_code',
            'service_id',
            'notes',
            'honeypot'
        ]);

        // Reset villages
        $this->villages = [];

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
