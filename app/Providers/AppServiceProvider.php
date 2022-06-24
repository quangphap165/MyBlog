<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        if (Schema::hasTable('categories')) {
            $categories = Category::withCount('posts')->orderBy('posts_count', 'DESC')->take(10)->get();
            View::share('navbar_categories', $categories);
            $setting = Setting::find(1);
            view::share('setting', $setting);
        }
    }
}
