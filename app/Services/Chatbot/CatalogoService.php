<?php

namespace App\Services\Chatbot;

class CatalogoService extends ChatbotService
{
    private const PRODUCTO = "SELECT p.id, p.name, " . self::PRECIOS . ",
            s.name AS subcategoria,
            " . self::IMAGEN_PRINCIPAL . " AS imagen_url";

    private const DESDE_PRODUCTO = "FROM product p
        JOIN subcategory s ON p.subcategory_id = s.id AND s.deleted_at IS NULL";

    /** #1 Productos con stock de una subcategoría. */
    public function buscarCategoria(string $subcategoria): array
    {
        $rows = $this->select(self::PRODUCTO . " " . self::DESDE_PRODUCTO . "
            WHERE s.name = ? AND " . self::PRODUCTO_VISIBLE . " AND " . $this->conStock() . "
            ORDER BY (imagen_url IS NULL) ASC, p.score DESC, p.price1 ASC
            LIMIT 3", [$subcategoria]);

        return $this->cast($rows, self::CAST_PRECIOS);
    }

    /** #2 Producto por nombre; con_stock distingue "no existe" de "agotado". */
    public function buscarNombre(string $termino): array
    {
        $rows = $this->select(self::PRODUCTO . ",
                " . $this->conStock() . " AS con_stock
            " . self::DESDE_PRODUCTO . "
            WHERE p.name LIKE CONCAT('%', ?, '%') AND " . self::PRODUCTO_VISIBLE . "
            ORDER BY con_stock DESC, p.score DESC
            LIMIT 2", [$termino]);

        return $this->cast($rows, self::CAST_PRECIOS + ['con_stock' => 'int']);
    }

    /** #3 Fallback: OR de LIKE, uno por palabra. */
    public function buscarPalabras(array $palabras): array
    {
        $likes = implode(' OR ', array_fill(0, count($palabras), "p.name LIKE CONCAT('%', ?, '%')"));

        // score va en el SELECT por el DISTINCT + ORDER BY; no es parte de la respuesta.
        $rows = $this->select("SELECT DISTINCT p.id, p.name, p.score, s.name AS subcategoria,
                " . self::IMAGEN_PRINCIPAL . " AS imagen_url
            " . self::DESDE_PRODUCTO . "
            WHERE ({$likes}) AND " . self::PRODUCTO_VISIBLE . " AND " . $this->conStock() . "
            ORDER BY p.score DESC
            LIMIT 3", array_values($palabras));

        return $this->without($rows, 'score');
    }

    /** #4 Colores con stock por producto y sus tallas con stock, ya agrupados. */
    public function variantes(array $ids): array
    {
        $rows = $this->select("SELECT DISTINCT pd.product_id, col.id AS color_id, col.name AS color,
                sz.id AS size_id, sz.name AS nombre
            FROM product_details pd
            JOIN color col ON col.id = pd.color_id AND col.deleted_at IS NULL
            JOIN size sz ON sz.id = pd.size_id AND sz.deleted_at IS NULL
            WHERE pd.product_id IN ({$this->placeholders($ids)})
              AND {$this->disponible()} > 0 AND pd.status = 1 AND pd.deleted_at IS NULL
            ORDER BY pd.product_id, col.name, sz.name", array_values($ids));

        // Equivale al GROUP_CONCAT del contrato, sin el tope group_concat_max_len.
        $variantes = [];
        foreach ($rows as $row) {
            $key = "{$row['product_id']}:{$row['color_id']}";
            $variantes[$key] ??= [
                'product_id' => $row['product_id'],
                'color_id' => $row['color_id'],
                'color' => $row['color'],
                'tallas' => [],
            ];
            $variantes[$key]['tallas'][] = ['size_id' => $row['size_id'], 'nombre' => $row['nombre']];
        }

        return array_values($variantes);
    }

    /** #5 Subcategoría + color (fragmento) + talla (exacta); color y talla opcionales. */
    public function multifiltro(string $subcategoria, ?string $color, ?string $talla): array
    {
        $where = ['s.name = ?'];
        $bindings = [$subcategoria];

        if ($color !== null) {
            $where[] = "col.name LIKE CONCAT('%', ?, '%')";
            $bindings[] = $color;
        }

        if ($talla !== null) {
            $where[] = 'sz.name = ?';
            $bindings[] = $talla;
        }

        $rows = $this->select("SELECT DISTINCT p.id, p.name, " . self::PRECIOS . ", p.score,
                s.name AS subcategoria,
                " . self::IMAGEN_PRINCIPAL . " AS imagen_url
            " . self::DESDE_PRODUCTO . "
            JOIN product_details pd ON p.id = pd.product_id AND pd.deleted_at IS NULL
            JOIN color col ON pd.color_id = col.id AND col.deleted_at IS NULL
            JOIN size sz ON pd.size_id = sz.id AND sz.deleted_at IS NULL
            WHERE " . implode(' AND ', $where) . "
              AND {$this->disponible()} > 0 AND pd.status = 1 AND " . self::PRODUCTO_VISIBLE . "
            ORDER BY (imagen_url IS NULL) ASC, p.score DESC, p.price1 ASC
            LIMIT 3", $bindings);

        return $this->cast($this->without($rows, 'score'), self::CAST_PRECIOS);
    }

    /** #6 Productos en liquidación con stock. */
    public function liquidacion(): array
    {
        $rows = $this->select(self::PRODUCTO . " " . self::DESDE_PRODUCTO . "
            WHERE " . self::PRODUCTO_VISIBLE . "
              AND p.liquidacion = 1 AND p.priceLiquidacion > 0
              AND " . $this->conStock() . "
            ORDER BY (imagen_url IS NULL) ASC, p.score DESC, p.price1 ASC
            LIMIT 3");

        return $this->cast($rows, self::CAST_PRECIOS);
    }

    /** #7 Fotos adicionales; OFFSET 1 salta la principal. */
    public function fotos(int $productId): array
    {
        return $this->select("SELECT i.url, col.name AS color
            FROM image i
            LEFT JOIN color col ON i.color_id = col.id AND col.deleted_at IS NULL
            WHERE i.product_id = ? AND i.deleted_at IS NULL
            ORDER BY i.id ASC
            LIMIT 3 OFFSET 1", [$productId]);
    }

    /** #8 Alternativas con stock: primero la subcategoría original, luego el grupo. */
    public function alternativas(array $subcatsGrupo, string $subcatOriginal, array $idsExcluir): array
    {
        // NOT IN () vacío es SQL inválido; 0 no es un id posible.
        $idsExcluir = $idsExcluir ?: [0];

        $rows = $this->select(self::PRODUCTO . " " . self::DESDE_PRODUCTO . "
            WHERE s.name IN ({$this->placeholders($subcatsGrupo)})
              AND " . self::PRODUCTO_VISIBLE . "
              AND p.id NOT IN ({$this->placeholders($idsExcluir)})
              AND " . $this->conStock() . "
            ORDER BY FIELD(s.name, ?) DESC, (imagen_url IS NULL) ASC, p.score DESC
            LIMIT 2", [...array_values($subcatsGrupo), ...array_values($idsExcluir), $subcatOriginal]);

        return $this->cast($rows, self::CAST_PRECIOS);
    }
}
