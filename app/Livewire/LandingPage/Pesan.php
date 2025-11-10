<?php

declare(strict_types=1);

namespace App\Livewire\LandingPage;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Transaction;
use App\Helper\InvoiceHelper;
use App\Helper\OrderRateLimiterHelper;
use App\Helper\WilayahHelper;
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
    public array $service_ids = []; // Multiple services
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

    public function mount(): void
    {
        // Load active services
        $this->search();

        // Load districts (kecamatan) di Kota Kendari
        $this->districts = WilayahHelper::getKendariDistricts();

        // Set form loaded timestamp untuk bot detection
        $this->form_loaded_at = now()->timestamp;
    }

    /**
     * Search services untuk x-choices searchable
     */
    public function search(string $value = ''): void
    {
        // Besides the search results, include the currently selected options
        $selectedOptions = !empty($this->service_ids)
            ? Service::whereIn('id', $this->service_ids)->get()
            : collect();

        $this->services = Service::query()
            ->where('is_active', true)
            ->where('name', 'like', "%$value%")
            ->orderBy('is_featured', 'desc') // Featured first
            ->orderBy('name')
            ->get()
            ->merge($selectedOptions);
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
    public function updatedDistrictCode(): void
    {
        // Reset village code
        $this->village_code = '';

        // Load villages berdasarkan district yang dipilih
        if (!empty($this->district_code)) {
            $this->villages = WilayahHelper::getVillagesByDistrict($this->district_code);
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
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:services,id',
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
            'service_ids.required' => 'Silakan pilih minimal satu layanan.',
            'service_ids.min' => 'Silakan pilih minimal satu layanan.',
            'service_ids.*.exists' => 'Layanan tidak valid.',
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
            $rateLimitResult = OrderRateLimiterHelper::checkAllLimits(
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
            $district = collect($this->districts)->firstWhere('code', $this->district_code);
            $village = collect($this->villages)->firstWhere('code', $this->village_code);

            $districtName = $district['name'] ?? '';
            $villageName = $village['name'] ?? '';

            // Prepare address data for JSONB
            $addressData = [
                'detail_address' => $this->detail_address,
                'village_code' => $this->village_code,
                'village_name' => $villageName,
                'district_code' => $this->district_code,
                'district_name' => $districtName,
                'city_code' => '74.71', // Kota Kendari
                'city_name' => 'Kota Kendari',
                'province_code' => '74',
                'province_name' => 'Sulawesi Tenggara',
                'is_default' => true,
            ];

            // Find or create customer by phone number
            $customer = Customer::where('phone', $this->phone)->first();

            if (!$customer) {
                // Create new customer with JSONB data structure
                $customer = Customer::create([
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'password' => 'pelanggan_main', // Default password untuk customer baru
                    'member' => false,
                    'data' => [
                        'name' => $this->name,
                        'addresses' => [$addressData],
                    ],
                ]);
            } else {
                // Customer sudah ada, update data
                $existingData = $customer->data ?? [];

                // Update name jika belum ada
                if (empty($existingData['name'])) {
                    $existingData['name'] = $this->name;
                }

                // Update email jika sebelumnya kosong dan sekarang diisi
                if (empty($customer->email) && !empty($this->email)) {
                    $customer->email = $this->email;
                }

                // Update atau tambahkan address
                $existingAddresses = $existingData['addresses'] ?? [];

                // Cari apakah address dengan village_code dan district_code ini sudah ada
                $addressExists = false;
                foreach ($existingAddresses as $index => $addr) {
                    if (($addr['village_code'] ?? '') === $this->village_code &&
                        ($addr['district_code'] ?? '') === $this->district_code) {
                        // Update existing address
                        $existingAddresses[$index] = $addressData;
                        $addressExists = true;
                        break;
                    }
                }

                // Jika address belum ada, tambahkan
                if (!$addressExists) {
                    // Set semua address lain jadi tidak default
                    foreach ($existingAddresses as $index => $addr) {
                        $existingAddresses[$index]['is_default'] = false;
                    }
                    $existingAddresses[] = $addressData;
                }

                $existingData['addresses'] = $existingAddresses;
                $customer->data = $existingData;
                $customer->save();
            }

            // Get selected services
            $services = Service::whereIn('id', $this->service_ids)->get();

            // Prepare items array for multiple services
            $items = [];
            $maxDurationHours = 0;

            foreach ($services as $service) {
                $pricingUnit = $service->data['pricing']['unit'] ?? 'per_kg';
                $pricePerKg = $service->data['pricing']['price_per_kg'] ?? null;
                $pricePerItem = $service->data['pricing']['price_per_item'] ?? null;
                $durationHours = $service->data['duration_hours'] ?? 72;

                // Track max duration untuk estimated finish date
                if ($durationHours > $maxDurationHours) {
                    $maxDurationHours = $durationHours;
                }

                $items[] = [
                    'service_id' => $service->id,
                    'service_name' => $service->name,
                    'pricing_unit' => $pricingUnit,
                    'price_per_kg' => $pricePerKg,
                    'price_per_item' => $pricePerItem,
                    'quantity' => 0, // Untuk per_item, akan diisi oleh kurir
                    'total_weight' => 0, // Untuk per_kg, akan ditimbang oleh kurir
                    'subtotal' => 0, // Akan dihitung setelah ditimbang/diinput quantity
                ];
            }

            // Calculate estimated finish date berdasarkan service terlama
            $estimatedFinishDate = now()->addHours($maxDurationHours);

            // Prepare transaction data for JSONB
            $transactionData = [
                'items' => $items,
                'notes' => $this->notes,
                'order_date' => now()->toDateTimeString(),
                'estimated_finish_date' => $estimatedFinishDate->toDateTimeString(),
            ];

            // Create transaction
            // Event akan otomatis di-broadcast via TransactionObserver
            $transaction = Transaction::create([
                'invoice_number' => InvoiceHelper::generateInvoiceNumber(),
                'customer_id' => $customer->id,
                'courier_id' => null, // Akan diassign saat kurir ambil pesanan
                'location_id' => null, // Akan diassign oleh admin
                'workflow_status' => 'pending_confirmation',
                'payment_timing' => $this->payment_timing,
                'payment_status' => 'unpaid',
                'data' => $transactionData,
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
                'service_ids',
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
            'service_ids',
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
