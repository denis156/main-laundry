<?php

namespace App\Livewire\Kurir\Components;

use Livewire\Attributes\On;
use Livewire\Component;
use Mary\Traits\Toast;

class FabInstallApp extends Component
{
    use Toast;
    public bool $installWebApp = false;

    public bool $showFab = false;

    public bool $isInstalled = false;

    public function mount()
    {
        // Check jika PWA sudah ter-install lewat JavaScript
        $this->js('$wire.checkPWAStatus()');
    }

    public function openConfirmationInstallApp()
    {
        $this->installWebApp = true;
    }

    public function closeConfirmationInstall()
    {
        $this->installWebApp = false;
    }

    public function installPWA()
    {
        // Trigger install prompt dari JavaScript
        $this->js(<<<'JS'
            (async () => {
                const outcome = await window.pwaInstall.prompt();

                if (outcome === 'accepted') {
                    $wire.handleInstallSuccess();
                } else if (outcome === 'dismissed') {
                    $wire.handleInstallDismissed();
                } else {
                    $wire.handleInstallUnavailable();
                }
            })();
        JS);
    }

    public function handleInstallSuccess()
    {
        $this->isInstalled = true;
        $this->installWebApp = false;
        $this->showFab = false;

        $this->success(
            title: 'Aplikasi Berhasil Diinstall!',
            description: 'Cek home screen Anda.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
    }

    public function handleInstallDismissed()
    {
        $this->installWebApp = false;

        $this->info(
            title: 'Install Dibatalkan',
            description: 'Anda bisa install kapan saja.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
    }

    public function handleInstallUnavailable()
    {
        $this->installWebApp = false;

        $this->warning(
            title: 'Install Tidak Tersedia',
            description: 'Aplikasi mungkin sudah ter-install.',
            position: 'toast-top toast-end',
            timeout: 3000
        );
    }

    public function checkPWAStatus()
    {
        // Check status dari JavaScript
        $this->js(<<<'JS'
            const isInstalled = window.pwaInstall.isInstalled();
            const canInstall = window.pwaInstall.canInstall();

            $wire.set('isInstalled', isInstalled);
            $wire.set('showFab', canInstall && !isInstalled);
        JS);
    }

    #[On('pwa-installable')]
    public function handlePWAInstallable()
    {
        // Event dari JavaScript saat beforeinstallprompt triggered
        // Double check apakah PWA sudah installed sebelum show FAB
        $this->js(<<<'JS'
            const isInstalled = window.pwaInstall.isInstalled();

            if (!isInstalled) {
                $wire.set('showFab', true);
                $wire.set('isInstalled', false);
            } else {
                // PWA sudah installed, jangan show FAB
                $wire.set('showFab', false);
                $wire.set('isInstalled', true);
            }
        JS);
    }

    #[On('pwa-installed')]
    public function handlePWAInstalled()
    {
        // Event dari JavaScript saat app ter-install
        $this->isInstalled = true;
        $this->showFab = false;
    }

    public function render()
    {
        return view('livewire.kurir.components.fab-install-app', [
            'appName' => config('app.name'),
            'appVersion' => config('app.kurir_version'),
        ]);
    }
}
