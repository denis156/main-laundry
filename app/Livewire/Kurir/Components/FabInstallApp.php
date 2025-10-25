<?php

namespace App\Livewire\Kurir\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class FabInstallApp extends Component
{
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

        $this->dispatch('notify', type: 'success', message: 'Aplikasi berhasil diinstall! Cek home screen Anda.');
    }

    public function handleInstallDismissed()
    {
        $this->installWebApp = false;
        $this->dispatch('notify', type: 'info', message: 'Install dibatalkan. Anda bisa install kapan saja.');
    }

    public function handleInstallUnavailable()
    {
        $this->installWebApp = false;
        $this->dispatch('notify', type: 'warning', message: 'Install tidak tersedia. Aplikasi mungkin sudah ter-install.');
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
        $this->showFab = true;
        $this->isInstalled = false;
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
