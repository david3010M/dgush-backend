<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Access;
use App\Models\Category;
use App\Models\GroupMenu;
use App\Models\OptionMenu;
use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(GroupMenuSeeder::class);
        $this->call(TypeUserSeeder::class);
        $this->call(OptionMenuSeeder::class);
        $this->call(AccessSeeder::class);
        $this->call(UserSeeder::class);

        $this->call(CategorySeeder::class);
        $this->call(SubcategorySeeder::class);
        $this->call(ColorSeeder::class);
        $this->call(SizeSeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(CommentSeeder::class);
        $this->call(ImageSeeder::class);

        $this->call(DepartmentSeeder::class);
        $this->call(ProvinceSeeder::class);
        $this->call(DistrictSeeder::class);

//        ProductDetailsSeeder
        $this->call(CouponSeeder::class);
        $this->call(ProductDetailsSeeder::class);
        $this->call(WishItemSeeder::class);

        $this->call(OrderSeeder::class);
        $this->call(OrderItemSeeder::class);
        $this->call(SendInformationSeeder::class);
        $this->call(BannerSeeder::class);

    }
}
