<script setup>
import { Head } from '@inertiajs/inertia-vue3';
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue';

defineProps({
    policy: String,
});
</script>

<template>
    <Head :title="$t('Privacy Policy')" />

    <div class="font-sans text-gray-900 antialiased">
        <div class="pt-4 bg-gray-100">
            <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0">
                <div>
                    <JetAuthenticationCardLogo />
                </div>

                <div class="w-full sm:max-w-2xl mt-6 p-6 bg-white shadow-md overflow-hidden sm:rounded-lg prose mb-6">
                    <h1 class="text-center">Privacy policy</h1>

                    <p>The customer portal supports the following browsers:</p>
                    <ul>
                        <li>The latest Edge browser</li>
                    </ul>
                    <ul>
                        <li>Firefox (latest)</li>
                    </ul>
                    <ul>
                        <li>Chrome (latest)</li>
                    </ul>
                    <ul>
                        <li>Safari (latest)</li>
                    </ul>
                    <p>We do not guarantee that the site will work with other browsers, but it&#x27;s very likely that it will just work.</p>
                    <h3>Rights</h3>
                    <p>You should provide your real name when you register to an account, because we will need it when we process the payment. You also need a valid email address if you want to upgrade your account, or receive reminders by email.</p>

                    <p>You have the right to close your account at any time.</p>

                    <p>You have the right to request an export of your data at any time, in the SQL format.</p>

                    <p>Your data will not be intentionally shown to other users or shared with third parties.</p>

                    <p>Your personal data will not be shared with anyone without your consent.</p>

                    <p>Your data is backed up every hour.</p>

                    <p>If the site ceases operation, you will receive an opportunity to export all your data before the site dies.</p>

                    <p>Any new features that affect privacy will be strictly opt-in.</p>
                    <h3>Responsibilities</h3>
                    <p>You will not use the site to store illegal information or data under the Canadian law (or any law).</p>

                    <p>You have to be at least 18+ to create an account and use the site (or in some countries, older depending on the age of being considered an adult).</p>

                    <p>You must not abuse the site by knowingly posting malicious code that could harm you or the other users.</p>

                    <p>You must only use the site to do things that are widely accepted as morally good.</p>

                    <p>You may not make automated requests to the site.</p>

                    <p>You may not try to take the site down by bombarding it with requests or otherwise.</p>

                    <p>You may not abuse the invitation system.</p>

                    <p>You are responsible for keeping your account secure.</p>

                    <p>We reserve the right to close accounts that abuse the system or use it in an unreasonable manner.</p>

                    <h3>Other important legal stuff</h3>
                    <p>Though we want to provide a great service, there are certain things about the service we cannot promise. For example, the services and software are provided “as-is”, at your own risk, without express or implied warranty or condition of any kind. We also disclaim any warranties of merchantability, fitness for a particular purpose or non-infringement. Monica will have no responsibility for any harm to your computer system, loss or corruption of data, or other harm that results from your access to or use of the Services or Software.</p>

                    <p>These Terms can change at any time, but we&#x27;ll never be a dick about it. Running this site is a dream come true to us, and we hope we&#x27;ll be able to run it as long as we can.</p>
                </div>
            </div>
        </div>
    </div>
</template>
