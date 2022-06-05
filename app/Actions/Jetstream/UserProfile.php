<?php

namespace App\Actions\Jetstream;

use Illuminate\Http\Request;

class UserProfile
{
    /**
     * Get user profile data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $data
     * @return array
     */
    public function __invoke(Request $request, array $data): array
    {
        /** @var \Illuminate\Support\Collection $providers */
        $providers = config('auth.login_providers');
        $providersName = $providers->mapWithKeys(function ($provider) {
            return [$provider => config("services.$provider.name") ?? __("auth.login_provider_{$provider}")];
        });

        $webauthnKeys = $request->user()->webauthnKeys()
            ->get()
            ->map(function ($key) {
                return [
                    'id' => $key->id,
                    'name' => $key->name,
                    'type' => $key->type,
                    'last_active' => $key->updated_at->diffForHumans(),
                ];
            })
            ->toArray();

        $data['providers'] = $providers;
        $data['providersName'] = $providersName;
        $data['userTokens'] = $request->user()->usertokens()->get();
        $data['webauthnKeys'] = $webauthnKeys;

        return $data;
    }
}
