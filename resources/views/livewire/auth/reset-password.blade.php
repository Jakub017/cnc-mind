<x-layouts.auth>
    <div class="flex-1 flex justify-center items-center p-4">
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
                __("Set a new password")
            }}</flux:heading>

            <!-- Session Status -->
            <x-auth-session-status
                class="text-center"
                :status="session('status')"
            />

            <form
                method="POST"
                action="{{ route('password.update') }}"
                class="flex flex-col gap-6"
            >
                @csrf
                <!-- Token -->
                <input
                    type="hidden"
                    name="token"
                    value="{{ request()->route('token') }}"
                />
                <!-- Email Address -->
                <flux:input
                    name="email"
                    value="{{ request('email') }}"
                    :label="__('Email')"
                    type="email"
                    required
                    autocomplete="email"
                />

                <!-- Password -->
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Password')"
                    viewable
                />

                <!-- Confirm Password -->
                <flux:input
                    name="password_confirmation"
                    :label="__('Confirm password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Confirm password')"
                    viewable
                />

                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full cursor-pointer"
                    data-test="reset-password-button"
                >
                    {{ __("Reset password") }}
                </flux:button>
            </form>
        </div>
    </div>
</x-layouts.auth>
