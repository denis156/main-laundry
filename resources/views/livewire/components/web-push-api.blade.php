{{-- Invisible component - no UI, hanya untuk handle API calls dari JavaScript --}}
<div x-data="{
    init() {
        // Register component ke global scope agar bisa diakses dari webPush.js
        window.WebPushApiComponent = @this;
    }
}" style="display: none;"></div>
