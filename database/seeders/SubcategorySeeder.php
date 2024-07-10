<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    protected $model = Subcategory::class;

    public function run(): void
    {
        // CREATE SUBCATEGORIES FOR "Vestidos"
        Subcategory::create([
            'name' => 'Vestidos Casuales',
            'value' => 'vestidos-casuales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/vestidos.png',
            'category_id' => 1,
        ]);

        Subcategory::create([
            'name' => 'Vestidos Formales',
            'value' => 'vestidos-formales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/vestidos.png',
            'category_id' => 1,
        ]);

        Subcategory::create([
            'name' => 'Vestidos Coctel',
            'value' => 'vestidos-coctel',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/vestidos.png',
            'category_id' => 1,
        ]);

        Subcategory::create([
            'name' => 'Vestidos de Noche',
            'value' => 'vestidos-de-noche',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/vestidos.png',
            'category_id' => 1,
        ]);

//        CREATE SUBCATEGORIES FOR "Blusas"
        Subcategory::create([
            'name' => 'Blusas Casuales',
            'value' => 'blusas-casuales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 2,
        ]);

        Subcategory::create([
            'name' => 'Blusas Formales',
            'value' => 'blusas-formales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 2,
        ]);

        Subcategory::create([
            'name' => 'Blusas Deportivas',
            'value' => 'blusas-deportivas',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 2,
        ]);

        Subcategory::create([
            'name' => 'Blusas de Noche',
            'value' => 'blusas-de-noche',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 2,
        ]);

//        CREATE SUBCATEGORIES FOR "Pantalones"
        Subcategory::create([
            'name' => 'Pantalones Casuales',
            'value' => 'pantalones-casuales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/pantalones.png',
            'category_id' => 3,
        ]);

        Subcategory::create([
            'name' => 'Pantalones Formales',
            'value' => 'pantalones-formales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/pantalones.png',
            'category_id' => 3,
        ]);

        Subcategory::create([
            'name' => 'Pantalones Deportivos',
            'value' => 'pantalones-deportivos',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/pantalones.png',
            'category_id' => 3,
        ]);

        Subcategory::create([
            'name' => 'Pantalones de Noche',
            'value' => 'pantalones-de-noche',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/pantalones.png',
            'category_id' => 3,
        ]);

//        CREATE SUBCATEGORIES FOR "Faldas"
        Subcategory::create([
            'name' => 'Faldas Casuales',
            'value' => 'faldas-casuales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/faldas.png',
            'category_id' => 4,
        ]);

        Subcategory::create([
            'name' => 'Faldas Formales',
            'value' => 'faldas-formales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/faldas.png',
            'category_id' => 4,
        ]);

        Subcategory::create([
            'name' => 'Faldas Deportivas',
            'value' => 'faldas-deportivas',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/faldas.png',
            'category_id' => 4,
        ]);

        Subcategory::create([
            'name' => 'Faldas de Noche',
            'value' => 'faldas-de-noche',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/faldas.png',
            'category_id' => 4,
        ]);

//        CREATE SUBCATEGORIES FOR "Shorts"
        Subcategory::create([
            'name' => 'Shorts Casuales',
            'value' => 'shorts-casuales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/faldas.png',
            'category_id' => 5,
        ]);

        Subcategory::create([
            'name' => 'Shorts Formales',
            'value' => 'shorts-formales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/faldas.png',
            'category_id' => 5,
        ]);

        Subcategory::create([
            'name' => 'Shorts Deportivos',
            'value' => 'shorts-deportivos',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/faldas.png',
            'category_id' => 5,
        ]);

//        CREATE SUBCATEGORIES FOR "Chaquetas"

        Subcategory::create([
            'name' => 'Chaquetas Casuales',
            'value' => 'chaquetas-casuales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 6,
        ]);

        Subcategory::create([
            'name' => 'Chaquetas Formales',
            'value' => 'chaquetas-formales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 6,
        ]);

        Subcategory::create([
            'name' => 'Chaquetas Deportivas',
            'value' => 'chaquetas-deportivas',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 6,
        ]);

        Subcategory::create([
            'name' => 'Chaquetas de Noche',
            'value' => 'chaquetas-de-noche',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 6,
        ]);

//        CREATE SUBCATEGORIES FOR "Sweaters"

        Subcategory::create([
            'name' => 'Sweaters Casuales',
            'value' => 'sweaters-casuales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 7,
        ]);

        Subcategory::create([
            'name' => 'Sweaters Formales',
            'value' => 'sweaters-formales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 7,
        ]);

        Subcategory::create([
            'name' => 'Sweaters Deportivos',
            'value' => 'sweaters-deportivos',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 7,
        ]);

        Subcategory::create([
            'name' => 'Sweaters de Noche',
            'value' => 'sweaters-de-noche',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 7,
        ]);

//        CREATE SUBCATEGORIES FOR "Camisas"

        Subcategory::create([
            'name' => 'Camisas Casuales',
            'value' => 'camisas-casuales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 8,
        ]);

        Subcategory::create([
            'name' => 'Camisas Formales',
            'value' => 'camisas-formales',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 8,
        ]);

        Subcategory::create([
            'name' => 'Camisas Deportivas',
            'value' => 'camisas-deportivas',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 8,
        ]);

        Subcategory::create([
            'name' => 'Camisas de Noche',
            'value' => 'camisas-de-noche',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 8,
        ]);

//        CREATE SUBCATEGORIES FOR "Trajes de Baño"

        Subcategory::create([
            'name' => 'Trajes Enterizos',
            'value' => 'trajes-enterizos',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/pantalones.png',
            'category_id' => 9,
        ]);

        Subcategory::create([
            'name' => 'Bikinis',
            'value' => 'bikinis',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/vestidos.png',
            'category_id' => 9,
        ]);

        Subcategory::create([
            'name' => 'Tankinis',
            'value' => 'tankinis',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/vestidos.png',
            'category_id' => 9,
        ]);

        Subcategory::create([
            'name' => 'Shorts de Baño',
            'value' => 'shorts-de-bano',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/vestidos.png',
            'category_id' => 9,
        ]);

//        CREATE SUBCATEGORIES FOR "Ropa Deportiva"

        Subcategory::create([
            'name' => 'Conjunto Ropa Deportiva',
            'value' => 'conjunto-ropa-deportiva',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 10,
        ]);

        Subcategory::create([
            'name' => 'Leggins Deportivos',
            'value' => 'leggins-deportivos',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/pantalones.png',
            'category_id' => 10,
        ]);

        Subcategory::create([
            'name' => 'Camisetas Deportivos',
            'value' => 'camisetas-deportivos',
            'image' => 'https://develop.garzasoft.com/dgush-backend/resources/image/polos.png',
            'category_id' => 10,
        ]);

    }
}
