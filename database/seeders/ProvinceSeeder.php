<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    protected $model = Province::class;

    public function run(): void
    {
        $array = [
//            AMAZONAS
            ['id' => 1, 'name' => 'Bagua', 'department_id' => 1],
            ['id' => 2, 'name' => 'Bongará', 'department_id' => 1],
            ['id' => 3, 'name' => 'Chachapoyas', 'department_id' => 1],
            ['id' => 4, 'name' => 'Condorcanqui', 'department_id' => 1],
            ['id' => 5, 'name' => 'Luya', 'department_id' => 1],
            ['id' => 6, 'name' => 'Rodríguez de Mendoza', 'department_id' => 1],
            ['id' => 7, 'name' => 'Utcubamba', 'department_id' => 1],

//            ANCASH
            ['id' => 8, 'name' => 'Aija', 'department_id' => 2],
            ['id' => 9, 'name' => 'Antonio Raymondi', 'department_id' => 2],
            ['id' => 10, 'name' => 'Asunción', 'department_id' => 2],
            ['id' => 11, 'name' => 'Bolognesi', 'department_id' => 2],
            ['id' => 12, 'name' => 'Carhuaz', 'department_id' => 2],
            ['id' => 13, 'name' => 'Carlos Fermín Fitzcarrald', 'department_id' => 2],
            ['id' => 14, 'name' => 'Casma', 'department_id' => 2],
            ['id' => 15, 'name' => 'Corongo', 'department_id' => 2],
            ['id' => 16, 'name' => 'Huaraz', 'department_id' => 2],
            ['id' => 17, 'name' => 'Huari', 'department_id' => 2],
            ['id' => 18, 'name' => 'Huarmey', 'department_id' => 2],
            ['id' => 19, 'name' => 'Huaylas', 'department_id' => 2],
            ['id' => 20, 'name' => 'Mariscal Luzuriaga', 'department_id' => 2],
            ['id' => 21, 'name' => 'Ocros', 'department_id' => 2],
            ['id' => 22, 'name' => 'Pallasca', 'department_id' => 2],
            ['id' => 23, 'name' => 'Pomabamba', 'department_id' => 2],
            ['id' => 24, 'name' => 'Recuay', 'department_id' => 2],
            ['id' => 25, 'name' => 'Santa', 'department_id' => 2],
            ['id' => 26, 'name' => 'Sihuas', 'department_id' => 2],
            ['id' => 27, 'name' => 'Yungay', 'department_id' => 2],

//            APURIMAC
            ['id' => 28, 'name' => 'Abancay', 'department_id' => 3],
            ['id' => 29, 'name' => 'Andahuaylas', 'department_id' => 3],
            ['id' => 30, 'name' => 'Antabamba', 'department_id' => 3],
            ['id' => 31, 'name' => 'Aymaraes', 'department_id' => 3],
            ['id' => 32, 'name' => 'Chincheros', 'department_id' => 3],
            ['id' => 33, 'name' => 'Cotabambas', 'department_id' => 3],
            ['id' => 34, 'name' => 'Grau', 'department_id' => 3],

//            AREQUIPA
            ['id' => 35, 'name' => 'Arequipa', 'department_id' => 4],
            ['id' => 36, 'name' => 'Camaná', 'department_id' => 4],
            ['id' => 37, 'name' => 'Caravelí', 'department_id' => 4],
            ['id' => 38, 'name' => 'Castilla', 'department_id' => 4],
            ['id' => 39, 'name' => 'Caylloma', 'department_id' => 4],
            ['id' => 40, 'name' => 'Condesuyos', 'department_id' => 4],
            ['id' => 41, 'name' => 'Islay', 'department_id' => 4],
            ['id' => 42, 'name' => 'La Unión', 'department_id' => 4],

//            AYACUCHO
            ['id' => 43, 'name' => 'Cangallo', 'department_id' => 5],
            ['id' => 44, 'name' => 'Huamanga', 'department_id' => 5],
            ['id' => 45, 'name' => 'Huanca Sancos', 'department_id' => 5],
            ['id' => 46, 'name' => 'Huanta', 'department_id' => 5],
            ['id' => 47, 'name' => 'La Mar', 'department_id' => 5],
            ['id' => 48, 'name' => 'Lucanas', 'department_id' => 5],
            ['id' => 49, 'name' => 'Parinacochas', 'department_id' => 5],
            ['id' => 50, 'name' => 'Pàucar del Sara Sara', 'department_id' => 5],
            ['id' => 51, 'name' => 'Sucre', 'department_id' => 5],
            ['id' => 52, 'name' => 'Víctor Fajardo', 'department_id' => 5],
            ['id' => 53, 'name' => 'Vilcas Huamán', 'department_id' => 5],

//            CAJAMARCA
            ['id' => 54, 'name' => 'Cajabamba', 'department_id' => 6],
            ['id' => 55, 'name' => 'Cajamarca', 'department_id' => 6],
            ['id' => 56, 'name' => 'Celendín', 'department_id' => 6],
            ['id' => 57, 'name' => 'Chota', 'department_id' => 6],
            ['id' => 58, 'name' => 'Contumazá', 'department_id' => 6],
            ['id' => 59, 'name' => 'Cutervo', 'department_id' => 6],
            ['id' => 60, 'name' => 'Hualgayoc', 'department_id' => 6],
            ['id' => 61, 'name' => 'Jaén', 'department_id' => 6],
            ['id' => 62, 'name' => 'San Ignacio', 'department_id' => 6],
            ['id' => 63, 'name' => 'San Marcos', 'department_id' => 6],
            ['id' => 64, 'name' => 'San Miguel', 'department_id' => 6],
            ['id' => 65, 'name' => 'San Pablo', 'department_id' => 6],
            ['id' => 66, 'name' => 'Santa Cruz', 'department_id' => 6],

//            CALLAO
            ['id' => 67, 'name' => 'Prov. Const. del Callao', 'department_id' => 7],

//            CUSCO
            ['id' => 68, 'name' => 'Acomayo', 'department_id' => 8],
            ['id' => 69, 'name' => 'Anta', 'department_id' => 8],
            ['id' => 70, 'name' => 'Calca', 'department_id' => 8],
            ['id' => 71, 'name' => 'Canas', 'department_id' => 8],
            ['id' => 72, 'name' => 'Canchis', 'department_id' => 8],
            ['id' => 73, 'name' => 'Chumbivilcas', 'department_id' => 8],
            ['id' => 74, 'name' => 'Cusco', 'department_id' => 8],
            ['id' => 75, 'name' => 'Espinar', 'department_id' => 8],
            ['id' => 76, 'name' => 'La Convención', 'department_id' => 8],
            ['id' => 77, 'name' => 'Paruro', 'department_id' => 8],
            ['id' => 78, 'name' => 'Paucartambo', 'department_id' => 8],
            ['id' => 79, 'name' => 'Quispicanchi', 'department_id' => 8],
            ['id' => 80, 'name' => 'Urubamba', 'department_id' => 8],

//            HUANCAVELICA
            ['id' => 81, 'name' => 'Acobamba', 'department_id' => 9],
            ['id' => 82, 'name' => 'Angaraes', 'department_id' => 9],
            ['id' => 83, 'name' => 'Castrovirreyna', 'department_id' => 9],
            ['id' => 84, 'name' => 'Churcampa', 'department_id' => 9],
            ['id' => 85, 'name' => 'Huancavelica', 'department_id' => 9],
            ['id' => 86, 'name' => 'Huaytará', 'department_id' => 9],
            ['id' => 87, 'name' => 'Tayacaja', 'department_id' => 9],

//            HUANUCO
            ['id' => 88, 'name' => 'Ambo', 'department_id' => 10],
            ['id' => 89, 'name' => 'Dos de Mayo', 'department_id' => 10],
            ['id' => 90, 'name' => 'Huacaybamba', 'department_id' => 10],
            ['id' => 91, 'name' => 'Huamalíes', 'department_id' => 10],
            ['id' => 92, 'name' => 'Huánuco', 'department_id' => 10],
            ['id' => 93, 'name' => 'Lauricocha ', 'department_id' => 10],
            ['id' => 94, 'name' => 'Leoncio Prado', 'department_id' => 10],
            ['id' => 95, 'name' => 'Marañón', 'department_id' => 10],
            ['id' => 96, 'name' => 'Pachitea', 'department_id' => 10],
            ['id' => 97, 'name' => 'Puerto Inca', 'department_id' => 10],
            ['id' => 98, 'name' => 'Yarowilca', 'department_id' => 10],

//            ICA
            ['id' => 99, 'name' => 'Chincha', 'department_id' => 11],
            ['id' => 100, 'name' => 'Ica', 'department_id' => 11],
            ['id' => 101, 'name' => 'Nazca', 'department_id' => 11],
            ['id' => 102, 'name' => 'Palpa', 'department_id' => 11],
            ['id' => 103, 'name' => 'Pisco', 'department_id' => 11],

//            JUNIN
            ['id' => 104, 'name' => 'Chanchamayo', 'department_id' => 12],
            ['id' => 105, 'name' => 'Chupaca', 'department_id' => 12],
            ['id' => 106, 'name' => 'Concepción', 'department_id' => 12],
            ['id' => 107, 'name' => 'Huancayo', 'department_id' => 12],
            ['id' => 108, 'name' => 'Jauja', 'department_id' => 12],
            ['id' => 109, 'name' => 'Junín', 'department_id' => 12],
            ['id' => 110, 'name' => 'Satipo', 'department_id' => 12],
            ['id' => 111, 'name' => 'Tarma', 'department_id' => 12],
            ['id' => 112, 'name' => 'Yauli', 'department_id' => 12],

//            LA LIBERTAD
            ['id' => 113, 'name' => 'Ascope', 'department_id' => 13],
            ['id' => 114, 'name' => 'Bolívar', 'department_id' => 13],
            ['id' => 115, 'name' => 'Chepén', 'department_id' => 13],
            ['id' => 116, 'name' => 'Gran Chimú', 'department_id' => 13],
            ['id' => 117, 'name' => 'Julcán', 'department_id' => 13],
            ['id' => 118, 'name' => 'Otuzco', 'department_id' => 13],
            ['id' => 119, 'name' => 'Pacasmayo', 'department_id' => 13],
            ['id' => 120, 'name' => 'Pataz', 'department_id' => 13],
            ['id' => 121, 'name' => 'Sánchez Carrión', 'department_id' => 13],
            ['id' => 122, 'name' => 'Santiago de Chuco', 'department_id' => 13],
            ['id' => 123, 'name' => 'Trujillo', 'department_id' => 13],
            ['id' => 124, 'name' => 'Virú', 'department_id' => 13],

//            LAMBAYEQUE
            ['id' => 125, 'name' => 'Chiclayo', 'department_id' => 14],
            ['id' => 126, 'name' => 'Ferreñafe', 'department_id' => 14],
            ['id' => 127, 'name' => 'Lambayeque', 'department_id' => 14],

//            LIMA
            ['id' => 128, 'name' => 'Barranca', 'department_id' => 15],
            ['id' => 129, 'name' => 'Cajatambo', 'department_id' => 15],
            ['id' => 130, 'name' => 'Canta', 'department_id' => 15],
            ['id' => 131, 'name' => 'Cañete', 'department_id' => 15],
            ['id' => 132, 'name' => 'Huaral', 'department_id' => 15],
            ['id' => 133, 'name' => 'Huarochirí', 'department_id' => 15],
            ['id' => 134, 'name' => 'Huaura', 'department_id' => 15],
            ['id' => 135, 'name' => 'Lima', 'department_id' => 15],
            ['id' => 136, 'name' => 'Oyón', 'department_id' => 15],
            ['id' => 137, 'name' => 'Yauyos', 'department_id' => 15],

//            LORETO
            ['id' => 138, 'name' => 'Alto Amazonas', 'department_id' => 16],
            ['id' => 139, 'name' => 'Datem del Marañón', 'department_id' => 16],
            ['id' => 140, 'name' => 'Loreto', 'department_id' => 16],
            ['id' => 141, 'name' => 'Mariscal Ramón Castilla', 'department_id' => 16],
            ['id' => 142, 'name' => 'Maynas', 'department_id' => 16],
            ['id' => 143, 'name' => 'Putumay', 'department_id' => 16],
            ['id' => 144, 'name' => 'Requena', 'department_id' => 16],
            ['id' => 145, 'name' => 'Ucayali', 'department_id' => 16],

//            MADRE DE DIOS
            ['id' => 146, 'name' => 'Manu', 'department_id' => 17],
            ['id' => 147, 'name' => 'Tahuamanu', 'department_id' => 17],
            ['id' => 148, 'name' => 'Tambopata', 'department_id' => 17],

//            MOQUEGUA
            ['id' => 149, 'name' => 'General Sánchez Cerro', 'department_id' => 18],
            ['id' => 150, 'name' => 'Ilo', 'department_id' => 18],
            ['id' => 151, 'name' => 'Mariscal Nieto', 'department_id' => 18],

//            PASCO
            ['id' => 152, 'name' => 'Daniel Alcides Carrión', 'department_id' => 19],
            ['id' => 153, 'name' => 'Oxapampa', 'department_id' => 19],
            ['id' => 154, 'name' => 'Pasco', 'department_id' => 19],

//              PIURA
            ['id' => 155, 'name' => 'Ayabaca', 'department_id' => 20],
            ['id' => 156, 'name' => 'Huancabamba', 'department_id' => 20],
            ['id' => 157, 'name' => 'Morropón', 'department_id' => 20],
            ['id' => 158, 'name' => 'Paita', 'department_id' => 20],
            ['id' => 159, 'name' => 'Piura', 'department_id' => 20],
            ['id' => 160, 'name' => 'Sechura', 'department_id' => 20],
            ['id' => 161, 'name' => 'Sullana', 'department_id' => 20],
            ['id' => 162, 'name' => 'Talara', 'department_id' => 20],

//            PUNO
            ['id' => 163, 'name' => 'Azángaro', 'department_id' => 21],
            ['id' => 164, 'name' => 'Carabaya', 'department_id' => 21],
            ['id' => 165, 'name' => 'Chucuito', 'department_id' => 21],
            ['id' => 166, 'name' => 'El Collao', 'department_id' => 21],
            ['id' => 167, 'name' => 'Huancané', 'department_id' => 21],
            ['id' => 168, 'name' => 'Lampa', 'department_id' => 21],
            ['id' => 169, 'name' => 'Melgar', 'department_id' => 21],
            ['id' => 170, 'name' => 'Moho', 'department_id' => 21],
            ['id' => 171, 'name' => 'Puno', 'department_id' => 21],
            ['id' => 172, 'name' => 'San Antonio de Putina', 'department_id' => 21],
            ['id' => 173, 'name' => 'San Román', 'department_id' => 21],
            ['id' => 174, 'name' => 'Sandia', 'department_id' => 21],
            ['id' => 175, 'name' => 'Yunguyo', 'department_id' => 21],

//            SAN MARTÍN
            ['id' => 176, 'name' => 'Bellavista', 'department_id' => 22],
            ['id' => 177, 'name' => 'El Dorado', 'department_id' => 22],
            ['id' => 178, 'name' => 'Huallaga', 'department_id' => 22],
            ['id' => 179, 'name' => 'Lamas', 'department_id' => 22],
            ['id' => 180, 'name' => 'Mariscal Cáceres', 'department_id' => 22],
            ['id' => 181, 'name' => 'Moyobamba', 'department_id' => 22],
            ['id' => 182, 'name' => 'Picota', 'department_id' => 22],
            ['id' => 183, 'name' => 'Rioja', 'department_id' => 22],
            ['id' => 184, 'name' => 'San Martín', 'department_id' => 22],
            ['id' => 185, 'name' => 'Tocache', 'department_id' => 22],

//            TACNA
            ['id' => 186, 'name' => 'Candarave', 'department_id' => 23],
            ['id' => 187, 'name' => 'Jorge Basadre', 'department_id' => 23],
            ['id' => 188, 'name' => 'Tacna', 'department_id' => 23],
            ['id' => 189, 'name' => 'Tarata', 'department_id' => 23],

//            TUMBES
            ['id' => 190, 'name' => 'Contralmirante Villar', 'department_id' => 24],
            ['id' => 191, 'name' => 'Tumbes', 'department_id' => 24],
            ['id' => 192, 'name' => 'Zarumilla', 'department_id' => 24],

//            UCAYALI
            ['id' => 193, 'name' => 'Atalaya', 'department_id' => 25],
            ['id' => 194, 'name' => 'Coronel Portillo', 'department_id' => 25],
            ['id' => 195, 'name' => 'Padre Abad', 'department_id' => 25],
            ['id' => 196, 'name' => 'Purús', 'department_id' => 25],


        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
