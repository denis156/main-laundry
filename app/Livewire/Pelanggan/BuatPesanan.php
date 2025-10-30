<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use App\Models\Pos;
use Mary\Traits\Toast;
use App\Models\Service;
use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Title('Buat Pesanan')]
#[Layout('components.layouts.pelanggan')]
class BuatPesanan extends Component
{
    use Toast;

    // Form properties
    public ?int $service_id = null;
    public string $payment_timing = '';
    public string $detail_address = '';
    public string $notes = '';
    public ?string $form_loaded_at = null;

    /**
     * Mount: Initialize form_loaded_at untuk anti-bot detection
     * Dan check jika ada service yang sudah dipilih dari redirect
     */
    public function mount(): void
    {
        $this->form_loaded_at = now()->toDateTimeString();

        // Check jika ada service yang sudah dipilih dari session (redirect dari halaman lain)
        if (session()->has('selected_service_id')) {
            $this->service_id = session()->get('selected_service_id');
            session()->forget('selected_service_id');
            session()->forget('selected_service_name');
        }
    }

    /**
     * Listen ke event service-selected dari ServiceCard component
     */
    #[On('service-selected')]
    public function selectService(int $serviceId): void
    {
        $this->service_id = $serviceId;
    }

    /**
     * Get active services untuk dropdown
     */
    #[Computed]
    public function services()
    {
        return Service::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get selected service info
     */
    #[Computed]
    public function selectedService()
    {
        if (!$this->service_id) {
            return null;
        }

        return Service::find($this->service_id);
    }

    /**
     * Validation rules
     */
    protected function rules(): array
    {
        return [
            'service_id' => 'required|exists:services,id',
            'payment_timing' => 'required|in:on_pickup,on_delivery',
            'detail_address' => 'required|string|min:10|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Custom validation messages
     */
    protected function messages(): array
    {
        return [
            'service_id.required' => 'Silakan pilih layanan terlebih dahulu',
            'service_id.exists' => 'Layanan yang dipilih tidak valid',
            'payment_timing.required' => 'Silakan pilih metode pembayaran',
            'payment_timing.in' => 'Metode pembayaran tidak valid',
            'detail_address.required' => 'Detail alamat harus diisi',
            'detail_address.min' => 'Detail alamat minimal 10 karakter',
            'detail_address.max' => 'Detail alamat maksimal 500 karakter',
            'notes.max' => 'Catatan maksimal 1000 karakter',
        ];
    }

    /**
     * Submit form and create transaction
     */
    public function submit(): void
    {
        // Validasi form
        $validated = $this->validate();

        // Anti-bot: Cek apakah form disubmit terlalu cepat (< 3 detik)
        if ($this->form_loaded_at) {
            $formLoadedTime = \Carbon\Carbon::parse($this->form_loaded_at);
            $submittedTime = now();
            $diffInSeconds = $formLoadedTime->diffInSeconds($submittedTime);

            if ($diffInSeconds < 3) {
                $this->error('Form submission terlalu cepat. Mohon coba lagi.');
                return;
            }
        }

        try {
            DB::beginTransaction();

            // Ambil data customer yang sedang login
            $customer = auth('customer')->user();

            // Ambil data service untuk durasi dan harga
            $service = Service::findOrFail($validated['service_id']);

            // Generate invoice number (format: INV/YYYYMMDD/XXXX)
            $invoiceNumber = $this->generateInvoiceNumber();

            // Generate tracking token (UUID)
            $trackingToken = (string) Str::uuid();

            // Hitung estimated finish date (order_date + duration_days)
            $orderDate = now();
            $estimatedFinishDate = $orderDate->copy()->addDays($service->duration_days);

            // Cari pos terdekat berdasarkan village_code customer
            // Jika tidak ada, pos_id akan null
            $pos = null;
            if ($customer->village_code) {
                $pos = Pos::where('is_active', true)
                    ->whereJsonContains('area', $customer->village_code)
                    ->first();
            }

            // Simpan customer IP dan user agent untuk security
            $customerIp = request()->ip();
            $customerUserAgent = request()->userAgent();

            // Gabungkan detail_address dengan alamat customer yang sudah ada
            $fullAddress = trim(implode(', ', array_filter([
                $customer->address,
                $validated['detail_address'],
            ])));

            // Update detail_address customer jika berbeda
            if ($customer->detail_address !== $validated['detail_address']) {
                $customer->update([
                    'detail_address' => $validated['detail_address'],
                    'address' => $fullAddress,
                ]);
            }

            // Buat transaksi baru
            Transaction::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'courier_motorcycle_id' => null, // Akan diassign nanti oleh admin/sistem
                'pos_id' => $pos?->id,
                'weight' => 0, // Akan diisi setelah kurir timbang
                'price_per_kg' => $service->price_per_kg, // Simpan harga saat ini untuk historical
                'total_price' => 0, // Akan dihitung setelah ditimbang
                'workflow_status' => 'pending_confirmation',
                'payment_timing' => $validated['payment_timing'],
                'payment_status' => 'unpaid',
                'notes' => $validated['notes'] ?? null,
                'order_date' => $orderDate,
                'estimated_finish_date' => $estimatedFinishDate,
                'actual_finish_date' => null,
                'tracking_token' => $trackingToken,
                'customer_ip' => $customerIp,
                'customer_user_agent' => $customerUserAgent,
                'form_loaded_at' => $this->form_loaded_at,
            ]);

            DB::commit();

            // Reset form
            $this->reset(['service_id', 'payment_timing', 'detail_address', 'notes']);

            // Tampilkan success message
            $this->success(
                'Pesanan berhasil dibuat! Nomor invoice: ' . $invoiceNumber,
                position: 'toast-top toast-center',
                timeout: 5000,
                redirectTo: route('pelanggan.pesanan')
            );

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error untuk debugging
            Log::error('Error creating transaction: ' . $e->getMessage(), [
                'customer_id' => auth('customer')->id(),
                'service_id' => $validated['service_id'] ?? null,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->error('Terjadi kesalahan saat membuat pesanan. Silakan coba lagi.');
        }
    }

    /**
     * Generate unique invoice number
     * Format: INV/YYYYMMDD/XXXX
     */
    private function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "INV/{$date}/";

        // Ambil invoice terakhir untuk hari ini
        $lastInvoice = Transaction::where('invoice_number', 'LIKE', $prefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract nomor urut dari invoice terakhir
            $lastNumber = (int) substr($lastInvoice->invoice_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            // Mulai dari 1 jika belum ada invoice hari ini
            $newNumber = 1;
        }

        // Format nomor urut dengan padding 4 digit
        return $prefix . str_pad((string) $newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.pelanggan.buat-pesanan');
    }
}
