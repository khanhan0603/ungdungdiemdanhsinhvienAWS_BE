<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // thêm dòng này ở đầu file

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
        // Giới hạn độ dài chuỗi mặc định cho các cột chuỗi trong cơ sở dữ liệu
        Schema::defaultStringLength(191); // thêm dòng này trong phương thức boot
    }
}
