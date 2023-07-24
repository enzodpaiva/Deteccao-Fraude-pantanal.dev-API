<?php

namespace App\Bussiness\Repository;

use Carbon\Carbon;
use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * @property int $_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */

abstract class ProductRepository extends Model
{
    use SoftDeletes;

    protected $collection = 'products';
    protected $primaryKey = '_id';

    /** @attributes - relationship
     * $idPitzi
     * $nome
     * $preco
     * $taxaProduto
     * $memoriaInterna
     */

    protected $attributes = [
        'idPitzi' => '',
        'nome' => '',
        'preco' => '',
        'taxaProduto' => '',
        'memoriaInterna' => '',
    ];

    public static function factory(): ProductRepository
    {
        return app(ProductRepository::class);
    }

    public function listProducts()
    {
        return self::all()->pluck('idPitzi')->toArray();
    }

    public function getProducts(array $array = [])
    {
        $query = self::query();
        if (isset($array['name'])) {
            $query->where('nome', 'LIKE', "%" . $array['name'] . "%");
        }

        if (isset($array['id'])) {
            $query->where('idPitzi', intval($array['id']));
        }

        return $query->paginate(10);
    }

}
