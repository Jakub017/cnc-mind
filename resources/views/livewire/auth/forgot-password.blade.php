<x-layouts.auth>
    <div class="flex-1 flex justify-center items-center">
        <div class="w-full max-w-md space-y-6">
            <div class="flex justify-center opacity-50">
                <a href="/" class="group flex items-center gap-3">
                    <span
                        class="text-xl font-semibold text-zinc-800 dark:text-white"
                        >CNC Mind</span
                    >
                </a>
            </div>

            <flux:heading class="text-center" size="xl">{{
                __("Forgot password")
            }}</flux:heading>

            <!-- Session Status -->
            <x-auth-session-status
                class="text-center"
                :status="session('status')"
            />

            <form
                method="POST"
                action="{{ route('password.email') }}"
                class="flex flex-col gap-6"
            >
                @csrf

                <flux:input
                    name="email"
                    :label="__('Email address')"
                    type="email"
                    required
                    autofocus
                    placeholder="email@example.com"
                />

                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full cursor-pointer"
                    data-test="email-password-reset-link-button"
                >
                    {{ __("Email password reset link") }}
                </flux:button>
            </form>
            <flux:subheading class="text-center">
                {{ __("Or, return to") }}
                <flux:link wire:navigate href="{{ route('login') }}">{{
                    __("log in")
                }}</flux:link>
            </flux:subheading>
        </div>
    </div>
</x-layouts.auth>
