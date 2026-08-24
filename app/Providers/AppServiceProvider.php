<?php
namespace App\Providers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Builder::macro('whereLike', function ($attributes, string $searchTerm) {
            /** @var Builder $this */
            return $this->where(function (Builder $query) use ($attributes, $searchTerm) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        str_contains($attribute, '.'),
                        function (Builder $q) use ($attribute, $searchTerm) {
                            [$relation, $field] = explode('.', $attribute, 2);

                            $q->orWhereHas($relation, function (Builder $rel) use ($field, $searchTerm) {
                                $rel->where($field, 'LIKE', "%{$searchTerm}%");
                            });
                        },
                        function (Builder $q) use ($attribute, $searchTerm) {
                            $q->orWhere($attribute, 'LIKE', "%{$searchTerm}%");
                        }
                    );
                }
            });
        });
    }
}
