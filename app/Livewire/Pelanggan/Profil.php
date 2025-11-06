<?php

declare(strict_types=1);

namespace App\Livewire\Pelanggan;

use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helper\WilayahHelper;

#[Title('Profil Saya')]
#[Layout('components.layouts.pelanggan')]
class Profil extends Component
{
    use WithFileUploads, Toast;

    // Form fields untuk update profil
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $district_code = '';
    public string $district_name = '';
    public string $village_code = '';
    public string $village_name = '';
    public string $detail_address = '';
    public string $address = '';
    public $avatar;

    // Wilayah list
    public array $districts = []; // List kecamatan di Kota Kendari
    public array $villages = []; // List kelurahan berdasarkan kecamatan

    // Form fields untuk update password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $customer = Auth::guard('customer')->user();

        if ($customer) {
            $this->name = $customer->name ?? '';
            $this->email = $customer->email ?? '';
            $this->phone = $customer->phone ?? '';
            $this->district_code = $customer->district_code ?? '';
            $this->district_name = $customer->district_name ?? '';
            $this->village_code = $customer->village_code ?? '';
            $this->village_name = $customer->village_name ?? '';
            $this->detail_address = $customer->detail_address ?? '';
            $this->address = $customer->address ?? '';
        }

        // Load districts (kecamatan) di Kota Kendari
        $this->districts = WilayahHelper::getKendariDistricts();

        // Load villages jika district sudah ada
        if (!empty($this->district_code)) {
            $this->villages = WilayahHelper::getVillagesByDistrict($this->district_code);
        }

        // Tampilkan toast dari session jika ada
        if (session()->has('warning')) {
            $this->warning(
                title: 'Profil Belum Lengkap!',
                description: session('warning'),
                position: 'toast-top toast-end',
                timeout: 5000
            );
        }

        if (session()->has('success')) {
            $this->success(
                title: 'Login Berhasil!',
                description: session('success'),
                position: 'toast-top toast-end',
                timeout: 3000
            );
        }
    }

    #[Computed]
    public function customer()
    {
        return Auth::guard('customer')->user();
    }

    
    #[Computed]
    public function hasChanges(): bool
    {
        $customer = $this->customer;

        if (!$customer) {
            return false;
        }

        // Cek apakah ada perubahan pada profil dengan trim untuk menghindari whitespace issue
        $profileChanged = trim($this->name) !== trim($customer->name)
            || trim($this->email) !== trim($customer->email)
            || trim($this->phone) !== trim($customer->phone)
            || trim($this->district_code) !== trim($customer->district_code ?? '')
            || trim($this->district_name) !== trim($customer->district_name ?? '')
            || trim($this->village_code) !== trim($customer->village_code ?? '')
            || trim($this->village_name) !== trim($customer->village_name ?? '')
            || trim($this->detail_address) !== trim($customer->detail_address ?? '')
            || trim($this->address) !== trim($customer->address ?? '');

        // Cek apakah ada input password
        $passwordFilled = !empty(trim($this->current_password))
            || !empty(trim($this->new_password))
            || !empty(trim($this->new_password_confirmation));

        return $profileChanged || $passwordFilled;
    }

    /**
     * Auto-clear password baru jika password lama dihapus
     */
    public function updatedCurrentPassword(): void
    {
        // Jika password lama dikosongkan, clear juga password baru
        if (empty($this->current_password)) {
            $this->new_password = '';
            $this->new_password_confirmation = '';
        }
    }

    /**
     * Load villages saat user pilih kecamatan dan populate district name
     */
    public function updatedDistrictCode(): void
    {
        // Reset village code dan name ketika district berubah
        $this->village_code = '';
        $this->village_name = '';

        // Load villages berdasarkan district yang dipilih
        if (!empty($this->district_code)) {
            $this->villages = WilayahHelper::getVillagesByDistrict($this->district_code);

            // Populate district name dari districts array
            $district = collect($this->districts)->firstWhere('code', $this->district_code);
            $this->district_name = $district['name'] ?? '';
        } else {
            $this->villages = [];
            $this->district_name = '';
        }
    }

    /**
     * Populate village name ketika village code berubah
     */
    public function updatedVillageCode(): void
    {
        // Populate village name dari villages array
        $village = collect($this->villages)->firstWhere('code', $this->village_code);
        $this->village_name = $village['name'] ?? '';
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

    protected function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $this->customer->id,
            'phone' => ['required', 'string', 'regex:/^8[0-9]{8,11}$/', 'min:9', 'max:13'],
            'district_code' => 'nullable|string|max:10',
            'district_name' => 'nullable|string|max:255',
            'village_code' => 'nullable|string|max:10',
            'village_name' => 'nullable|string|max:255',
            'detail_address' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.min' => 'Nama minimal 3 karakter.',
            'name.max' => 'Nama maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pelanggan lain.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.regex' => 'Format nomor telepon tidak valid. Contoh: 81234567890',
            'phone.min' => 'Nomor telepon minimal 9 karakter.',
            'phone.max' => 'Nomor telepon maksimal 13 karakter.',
            'district_code.max' => 'Kode kecamatan maksimal 10 karakter.',
            'district_name.max' => 'Nama kecamatan maksimal 255 karakter.',
            'village_code.max' => 'Kode desa/kelurahan maksimal 10 karakter.',
            'village_name.max' => 'Nama desa/kelurahan maksimal 255 karakter.',
            'detail_address.max' => 'Detail alamat maksimal 500 karakter.',
            'address.max' => 'Alamat maksimal 500 karakter.',
            'current_password.required' => 'Password lama wajib diisi jika ingin mengubah password.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ];
    }

    public function cancel(): void
    {
        // Reset form ke nilai dari database
        $customer = $this->customer;

        if ($customer) {
            $this->name = $customer->name ?? '';
            $this->email = $customer->email ?? '';
            $this->phone = $customer->phone ?? '';
            $this->district_code = $customer->district_code ?? '';
            $this->district_name = $customer->district_name ?? '';
            $this->village_code = $customer->village_code ?? '';
            $this->village_name = $customer->village_name ?? '';
            $this->detail_address = $customer->detail_address ?? '';
            $this->address = $customer->address ?? '';

            // Load villages yang sesuai dengan district dari database
            if (!empty($this->district_code)) {
                $this->villages = WilayahHelper::getVillagesByDistrict($this->district_code);
            } else {
                $this->villages = [];
            }
        }

        // Reset password fields
        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        // Clear validation errors
        $this->resetValidation();
    }

    public function save(): void
    {
        // Format phone number untuk database
        $this->phone = $this->formatPhoneForDatabase($this->phone);

        // Validasi profil menggunakan rules()
        $this->validate($this->rules());

        // Update profil
        $this->customer->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'district_code' => $this->district_code ?: null,
            'district_name' => $this->district_name ?: null,
            'village_code' => $this->village_code ?: null,
            'village_name' => $this->village_name ?: null,
            'detail_address' => $this->detail_address ?: null,
            'address' => $this->address ?: null,
        ]);

        // Refresh customer data setelah update
        $this->customer->refresh();

        // Update password jika diisi
        if (!empty($this->current_password) || !empty($this->new_password)) {
            $this->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed',
            ]);

            // Verifikasi password lama
            if (!Hash::check($this->current_password, $this->customer->password)) {
                $this->error(
                    title: 'Password Salah!',
                    description: 'Password lama tidak sesuai.',
                    position: 'toast-top toast-end',
                    timeout: 3000
                );
                return;
            }

            // Update password baru
            $this->customer->update([
                'password' => Hash::make($this->new_password),
            ]);

            // Reset form password
            $this->current_password = '';
            $this->new_password = '';
            $this->new_password_confirmation = '';
        }

        $this->success(
            title: 'Profil Diperbarui!',
            description: 'Profil Anda berhasil diperbarui.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
    }

    public function updatedAvatar(): void
    {
        $this->validate([
            'avatar' => 'required|image|max:2048', // Max 2MB
        ]);

        // Hapus avatar lama jika ada
        if ($this->customer->avatar_url) {
            Storage::disk('public')->delete($this->customer->avatar_url);
        }

        // Simpan avatar baru
        $filename = 'avatar-' . $this->customer->id . '-' . time() . '.' . $this->avatar->getClientOriginalExtension();
        $path = $this->avatar->storeAs('avatars', $filename, 'public');

        $this->customer->update([
            'avatar_url' => $path,
        ]);

        $this->avatar = null;
        $this->success(
            title: 'Foto Profil Diperbarui!',
            description: 'Foto profil Anda berhasil diperbarui.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
    }

    public function logout(): void
    {
        Auth::guard('customer')->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('pelanggan.login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pelanggan.profil');
    }
}
