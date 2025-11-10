<?php

declare(strict_types=1);

namespace App\Livewire\Kurir;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Mary\Traits\Toast;
use App\Helper\Database\CourierHelper;

#[Title('Profil Kurir')]
#[Layout('components.layouts.kurir')]
class Profil extends Component
{
    use WithFileUploads, Toast;

    // Form fields untuk update profil
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $vehicle_number = '';
    public $avatar;

    // Form fields untuk update password
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $courier = Auth::guard('courier')->user();

        if ($courier) {
            $this->name = CourierHelper::getName($courier);
            $this->email = $courier->email;
            $this->phone = CourierHelper::getPhone($courier) ?? '';
            $this->vehicle_number = CourierHelper::getVehicleNumber($courier) ?? '';
        }
    }

    #[Computed]
    public function courier()
    {
        return Auth::guard('courier')->user();
    }

    #[Computed]
    public function assignedPos()
    {
        $courier = $this->courier;
        if (!$courier || !$courier->assigned_location_id) {
            return null;
        }
        return $courier->assignedLocation;
    }

    #[Computed]
    public function totalTransactions()
    {
        return $this->courier?->transactions()->count() ?? 0;
    }

    #[Computed]
    public function totalPayments()
    {
        return $this->courier?->payments()->count() ?? 0;
    }

    #[Computed]
    public function totalEarnings()
    {
        return $this->courier?->payments()->sum('amount') ?? 0;
    }

    #[Computed]
    public function hasChanges(): bool
    {
        $courier = $this->courier;

        if (!$courier) {
            return false;
        }

        // Cek apakah ada perubahan pada profil dengan trim untuk menghindari whitespace issue
        $profileChanged = trim($this->name) !== trim(CourierHelper::getName($courier))
            || trim($this->email) !== trim($courier->email)
            || trim($this->phone) !== trim(CourierHelper::getPhone($courier) ?? '')
            || trim($this->vehicle_number) !== trim(CourierHelper::getVehicleNumber($courier) ?? '');

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
            'email' => 'required|email|max:255|unique:couriers,email,' . $this->courier->id,
            'phone' => ['required', 'string', 'regex:/^8[0-9]{8,11}$/', 'min:9', 'max:13'],
            'vehicle_number' => 'required|string|max:20',
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
            'email.unique' => 'Email sudah digunakan oleh kurir lain.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid. Contoh: 81234567890',
            'phone.min' => 'Nomor WhatsApp minimal 9 karakter.',
            'phone.max' => 'Nomor WhatsApp maksimal 13 karakter.',
            'vehicle_number.required' => 'Nomor kendaraan wajib diisi.',
            'vehicle_number.max' => 'Nomor kendaraan maksimal 20 karakter.',
            'current_password.required' => 'Password lama wajib diisi jika ingin mengubah password.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ];
    }

    public function cancel(): void
    {
        // Reset form ke nilai dari database
        $courier = $this->courier;

        if ($courier) {
            $this->name = CourierHelper::getName($courier);
            $this->email = $courier->email;
            $this->phone = CourierHelper::getPhone($courier) ?? '';
            $this->vehicle_number = CourierHelper::getVehicleNumber($courier) ?? '';
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

        // Update profil - update JSONB data
        $data = $this->courier->data ?? [];
        $data['name'] = $this->name;
        $data['phone'] = $this->phone;
        $data['vehicle_number'] = $this->vehicle_number;

        $this->courier->update([
            'email' => $this->email,
            'data' => $data,
        ]);

        // Refresh courier data setelah update
        $this->courier->refresh();

        // Update password jika diisi
        if (!empty($this->current_password) || !empty($this->new_password)) {
            $this->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            // Verifikasi password lama
            if (!Hash::check($this->current_password, $this->courier->password)) {
                $this->error(
                    title: 'Password Salah!',
                    description: 'Password lama tidak sesuai.',
                    position: 'toast-top toast-end',
                    timeout: 3000
                );
                return;
            }

            // Update password baru
            $this->courier->update([
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

        // Get avatar URL lama
        $oldAvatarUrl = CourierHelper::getAvatarUrl($this->courier);

        // Hapus avatar lama jika ada dan bukan dari URL eksternal
        if ($oldAvatarUrl && !filter_var($oldAvatarUrl, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($oldAvatarUrl);
        }

        // Simpan avatar baru
        $filename = 'avatar-' . $this->courier->id . '-' . time() . '.' . $this->avatar->getClientOriginalExtension();
        $path = $this->avatar->storeAs('avatars', $filename, 'public');

        // Update JSONB data dengan avatar URL baru
        $data = $this->courier->data ?? [];
        $data['avatar_url'] = $path;

        $this->courier->update([
            'data' => $data,
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
        Auth::guard('courier')->logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('kurir.login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.kurir.profil');
    }
}
