<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    protected $model = Permission::class;

    public function run(): void
    {
//        GROUP MENUS
        Permission::create([
            'name' => 'Ver Grupos de Menú',
            'route' => 'groupmenu.index',
        ]);

        Permission::create([
            'name' => 'Ver Grupo de Menú',
            'route' => 'groupmenu.show',
        ]);

        Permission::create([
            'name' => 'Crear Grupos de Menú',
            'route' => 'groupmenu.store',
        ]);

        Permission::create([
            'name' => 'Editar Grupos de Menú',
            'route' => 'groupmenu.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Grupos de Menú',
            'route' => 'groupmenu.destroy',
        ]);

//        OPTION MENUS
        Permission::create([
            'name' => 'Ver Opciones de Menú',
            'route' => 'optionmenu.index',
        ]);

        Permission::create([
            'name' => 'Ver Opcion de Menú',
            'route' => 'optionmenu.show',
        ]);

        Permission::create([
            'name' => 'Crear Opciones de Menú',
            'route' => 'optionmenu.store',
        ]);

        Permission::create([
            'name' => 'Editar Opciones de Menú',
            'route' => 'optionmenu.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Opciones de Menú',
            'route' => 'optionmenu.destroy',
        ]);

//        TYPE USERS

        Permission::create([
            'name' => 'Ver Tipos de Usuario',
            'route' => 'typeuser.index',
        ]);

        Permission::create([
            'name' => 'Ver Tipo de Usuario',
            'route' => 'typeuser.show',
        ]);

        Permission::create([
            'name' => 'Crear Tipos de Usuario',
            'route' => 'typeuser.store',
        ]);

        Permission::create([
            'name' => 'Editar Tipos de Usuario',
            'route' => 'typeuser.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Tipos de Usuario',
            'route' => 'typeuser.destroy',
        ]);

//        USERS

        Permission::create([
            'name' => 'Ver Usuarios',
            'route' => 'user.index',
        ]);

        Permission::create([
            'name' => 'Ver Usuario',
            'route' => 'user.show',
        ]);

        Permission::create([
            'name' => 'Crear Usuarios',
            'route' => 'user.store',
        ]);

        Permission::create([
            'name' => 'Editar Usuarios',
            'route' => 'user.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Usuarios',
            'route' => 'user.destroy',
        ]);

//        ACCESS

        Permission::create([
            'name' => 'Ver Accesos',
            'route' => 'access.index',
        ]);

        Permission::create([
            'name' => 'Ver Acceso',
            'route' => 'access.show',
        ]);

        Permission::create([
            'name' => 'Crear Accesos',
            'route' => 'access.store',
        ]);

        Permission::create([
            'name' => 'Editar Accesos',
            'route' => 'access.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Accesos',
            'route' => 'access.destroy',
        ]);

//        PERMISSION

        Permission::create([
            'name' => 'Ver Permisos',
            'route' => 'permission.index',
        ]);

        Permission::create([
            'name' => 'Ver Permiso',
            'route' => 'permission.show',
        ]);

        Permission::create([
            'name' => 'Crear Permisos',
            'route' => 'permission.store',
        ]);

        Permission::create([
            'name' => 'Editar Permisos',
            'route' => 'permission.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Permisos',
            'route' => 'permission.destroy',
        ]);

//        HAS PERMISSION

        Permission::create([
            'name' => 'Ver Permisos de Usuario',
            'route' => 'haspermission.index',
        ]);

        Permission::create([
            'name' => 'Ver Permiso de Usuario',
            'route' => 'haspermission.show',
        ]);

        Permission::create([
            'name' => 'Crear Permisos de Usuario',
            'route' => 'haspermission.store',
        ]);

        Permission::create([
            'name' => 'Editar Permisos de Usuario',
            'route' => 'haspermission.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Permisos de Usuario',
            'route' => 'haspermission.destroy',
        ]);

//          PRODUCT

        Permission::create([
            'name' => 'Ver Productos',
            'route' => 'product.index',
        ]);

        Permission::create([
            'name' => 'Ver Producto',
            'route' => 'product.show',
        ]);

        Permission::create([
            'name' => 'Crear Productos',
            'route' => 'product.store',
        ]);

        Permission::create([
            'name' => 'Editar Productos',
            'route' => 'product.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Productos',
            'route' => 'product.destroy',
        ]);

        Permission::create([
            'name' => 'Todos Productos',
            'route' => 'product.all',
        ]);

        Permission::create([
            'name' => 'Buscar Productos',
            'route' => 'product.search',
        ]);

        Permission::create([
            'name' => 'Set Colores de Producto',
            'route' => 'product.colors',
        ]);

        Permission::create([
            'name' => 'Set Tallas de Producto',
            'route' => 'product.sizes',
        ]);


//        CATEGORY

        Permission::create([
            'name' => 'Ver Categorías',
            'route' => 'category.index',
        ]);

        Permission::create([
            'name' => 'Ver Categoría',
            'route' => 'category.show',
        ]);

        Permission::create([
            'name' => 'Crear Categorías',
            'route' => 'category.store',
        ]);

        Permission::create([
            'name' => 'Editar Categorías',
            'route' => 'category.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Categorías',
            'route' => 'category.destroy',
        ]);

//        SUBCATEGORY

        Permission::create([
            'name' => 'Ver Subcategorías',
            'route' => 'subcategory.index',
        ]);

        Permission::create([
            'name' => 'Ver Subcategoría',
            'route' => 'subcategory.show',
        ]);

        Permission::create([
            'name' => 'Crear Subcategorías',
            'route' => 'subcategory.store',
        ]);

        Permission::create([
            'name' => 'Editar Subcategorías',
            'route' => 'subcategory.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Subcategorías',
            'route' => 'subcategory.destroy',
        ]);

//        COLOR

        Permission::create([
            'name' => 'Ver Colores',
            'route' => 'color.index',
        ]);

        Permission::create([
            'name' => 'Ver Color',
            'route' => 'color.show',
        ]);

        Permission::create([
            'name' => 'Crear Colores',
            'route' => 'color.store',
        ]);

        Permission::create([
            'name' => 'Editar Colores',
            'route' => 'color.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Colores',
            'route' => 'color.destroy',
        ]);

//        SIZE

        Permission::create([
            'name' => 'Ver Tallas',
            'route' => 'size.index',
        ]);

        Permission::create([
            'name' => 'Ver Talla',
            'route' => 'size.show',
        ]);

        Permission::create([
            'name' => 'Crear Tallas',
            'route' => 'size.store',
        ]);

        Permission::create([
            'name' => 'Editar Tallas',
            'route' => 'size.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Tallas',
            'route' => 'size.destroy',
        ]);

//        PRODUCT COLOR

        Permission::create([
            'name' => 'Ver Colores de Producto',
            'route' => 'productcolor.index',
        ]);

        Permission::create([
            'name' => 'Ver Color de Producto',
            'route' => 'productcolor.show',
        ]);

        Permission::create([
            'name' => 'Crear Colores de Producto',
            'route' => 'productcolor.store',
        ]);

        Permission::create([
            'name' => 'Editar Colores de Producto',
            'route' => 'productcolor.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Colores de Producto',
            'route' => 'productcolor.destroy',
        ]);

//            PRODUCT SIZE

        Permission::create([
            'name' => 'Ver Tallas de Producto',
            'route' => 'productsize.index',
        ]);

        Permission::create([
            'name' => 'Ver Talla de Producto',
            'route' => 'productsize.show',
        ]);

        Permission::create([
            'name' => 'Crear Tallas de Producto',
            'route' => 'productsize.store',
        ]);

        Permission::create([
            'name' => 'Editar Tallas de Producto',
            'route' => 'productsize.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Tallas de Producto',
            'route' => 'productsize.destroy',
        ]);

//        COMMENT

        Permission::create([
            'name' => 'Ver Comentarios',
            'route' => 'comment.index',
        ]);

        Permission::create([
            'name' => 'Ver Comentario',
            'route' => 'comment.show',
        ]);

        Permission::create([
            'name' => 'Crear Comentarios',
            'route' => 'comment.store',
        ]);

        Permission::create([
            'name' => 'Editar Comentarios',
            'route' => 'comment.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Comentarios',
            'route' => 'comment.destroy',
        ]);

//        COUPON
        Permission::create([
            'name' => 'Ver Cupones',
            'route' => 'coupon.index',
        ]);

        Permission::create([
            'name' => 'Ver Cupón',
            'route' => 'coupon.show',
        ]);

        Permission::create([
            'name' => 'Crear Cupones',
            'route' => 'coupon.store',
        ]);

        Permission::create([
            'name' => 'Editar Cupones',
            'route' => 'coupon.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Cupones',
            'route' => 'coupon.destroy',
        ]);

//        PROVINCE

        Permission::create([
            'name' => 'Ver Provincias',
            'route' => 'province.index',
        ]);

        Permission::create([
            'name' => 'Ver Provincia',
            'route' => 'province.show',
        ]);

        Permission::create([
            'name' => 'Crear Provincias',
            'route' => 'province.store',
        ]);

        Permission::create([
            'name' => 'Editar Provincias',
            'route' => 'province.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Provincias',
            'route' => 'province.destroy',
        ]);

//        DISTRICT

        Permission::create([
            'name' => 'Ver Distritos',
            'route' => 'district.index',
        ]);

        Permission::create([
            'name' => 'Ver Distrito',
            'route' => 'district.show',
        ]);

        Permission::create([
            'name' => 'Crear Distritos',
            'route' => 'district.store',
        ]);

        Permission::create([
            'name' => 'Editar Distritos',
            'route' => 'district.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Distritos',
            'route' => 'district.destroy',
        ]);

//            SEND INFORMATION

        Permission::create([
            'name' => 'Ver Envíos de Información',
            'route' => 'sendinformation.index',
        ]);

        Permission::create([
            'name' => 'Ver Envío de Información',
            'route' => 'sendinformation.show',
        ]);

        Permission::create([
            'name' => 'Crear Envíos de Información',
            'route' => 'sendinformation.store',
        ]);

        Permission::create([
            'name' => 'Editar Envíos de Información',
            'route' => 'sendinformation.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Envíos de Información',
            'route' => 'sendinformation.destroy',
        ]);

//        ORDER

        Permission::create([
            'name' => 'Ver Pedidos',
            'route' => 'order.index',
        ]);

        Permission::create([
            'name' => 'Ver Pedido',
            'route' => 'order.show',
        ]);

        Permission::create([
            'name' => 'Crear Pedidos',
            'route' => 'order.store',
        ]);

        Permission::create([
            'name' => 'Editar Pedidos',
            'route' => 'order.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Pedidos',
            'route' => 'order.destroy',
        ]);

//        WISH ITEM
        Permission::create([
            'name' => 'Ver Lista de Deseos',
            'route' => 'wishitem.index',
        ]);

        Permission::create([
            'name' => 'Ver Deseo',
            'route' => 'wishitem.show',
        ]);

        Permission::create([
            'name' => 'Crear Deseo',
            'route' => 'wishitem.store',
        ]);

        Permission::create([
            'name' => 'Editar Deseo',
            'route' => 'wishitem.update',
        ]);

        Permission::create([
            'name' => 'Eliminar Deseo',
            'route' => 'wishitem.destroy',
        ]);
    }
}
