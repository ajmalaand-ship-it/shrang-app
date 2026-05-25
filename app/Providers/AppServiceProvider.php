<?php
namespace App\Providers;
use App\Models\Clip;
use App\Policies\ClipPolicy;
use App\Services\AI\AIProviderInterface;
use App\Services\AI\AIService;
use App\Services\AI\AIUsageTracker;
use App\Services\AI\GeminiProvider;
use App\Services\AI\LyriaProvider;
use App\Services\AI\StabilityProvider;
use App\Services\AI\ElevenLabsProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AIProviderInterface::class, function ($app) {
            return match (config("ai.default_music_provider", "lyria")) {
                "gemini"     => new GeminiProvider(),
                "stability"  => new StabilityProvider(),
                "elevenlabs" => new ElevenLabsProvider(),
                default      => new LyriaProvider(),
            };
        });
        $this->app->singleton(AIService::class, function ($app) {
            return new AIService(
                $app->make(AIProviderInterface::class),
                new AIUsageTracker()
            );
        });
    }
    public function boot(): void
    {
        $this->app->useLangPath(resource_path("lang"));
        Gate::policy(Clip::class, ClipPolicy::class);
    }
}
