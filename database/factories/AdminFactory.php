<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         // Mảng họ, tên đệm, tên phổ biến tiếng Việt có dấu
        $ho = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Vũ', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Ngô', 'Dương', 'Phan', 'Trương'];
        $tenDem = ['Văn', 'Thị', 'Hữu', 'Đức', 'Minh', 'Ngọc', 'Thanh', 'Thu', 'Quốc', 'Xuân', 'Phương', 'Anh', 'Thế', 'Trọng'];
        $ten = ['An', 'Bình', 'Chi', 'Dũng', 'Giang', 'Hà', 'Hạnh', 'Hiếu', 'Hương', 'Hùng', 'Khang', 'Lan', 'Linh', 'Long', 'Mai', 'Nam', 'Nga', 'Ngọc', 'Phương', 'Quang', 'Sơn', 'Thảo', 'Trang', 'Tuấn', 'Tùng', 'Vân', 'Yến'];
        // Tạo tên Việt ngẫu nhiên
        $hoten = $this->faker->randomElement($ho) . ' ' .
                 $this->faker->randomElement($tenDem) . ' ' .
                 $this->faker->randomElement($ten);
        return [
            'hoten' => $hoten,
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('123456'), // Mật khẩu mặc định
            // Nếu chức vụ chỉ có một giá trị cố định, bạn có thể đặt trực tiếp
            'chucvu' => 'Cán bộ giáo vụ',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
