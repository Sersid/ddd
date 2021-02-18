<?php
declare(strict_types=1);

namespace App\Catalog\Product\Infrastructure;

use App\Catalog\Product\Domain\Entity\Brand;
use App\Catalog\Product\Domain\Entity\Code;
use App\Catalog\Product\Domain\Entity\Description;
use App\Catalog\Product\Domain\Entity\Id;
use App\Catalog\Product\Domain\Entity\IProductRepository;
use App\Catalog\Product\Domain\Entity\Name;
use App\Catalog\Product\Domain\Entity\Price;
use App\Catalog\Product\Domain\Entity\Product;
use App\Catalog\Product\Domain\Exception\ProductNotFound;
use App\Kernel\Hydrator;

class InMemoryProductRepository implements IProductRepository
{
    /** @var Product[] */
    private static array $arProducts;
    private Hydrator $hydrator;
    private int $id;

    public function __construct(Hydrator $hydrator)
    {
        $this->hydrator = $hydrator;
        $this->id = 1;
        self::$arProducts = [
            $hydrator->hydrate(Product::class, [
                'id' => new Id($this->id++),
                'code' => new Code(100001),
                'name' => new Name('Product name #100001'),
                'brand' => new Brand('Brand name'),
                'price' => new Price(100500),
                'description' => new Description(null),
            ]),
            $hydrator->hydrate(Product::class, [
                'id' => new Id($this->id++),
                'code' => new Code(100002),
                'name' => new Name('Product name #100002'),
                'brand' => new Brand('Brand name'),
                'price' => new Price(100501),
                'description' => new Description('Product description #100002'),
            ]),
        ];
    }

    public function add(Product $product): void
    {
        self::$arProducts[] = $product;
        $this->hydrator->setPropertyValue($product, 'id', new Id($this->id++));
    }

    /**
     * @param Id $id
     *
     * @return Product
     * @throws ProductNotFound
     */
    public function getById(Id $id): Product
    {
        foreach (self::$arProducts as $product) {
            if ($product->getId()->equalTo($id)) {
                return $product;
            }
        }
        throw new ProductNotFound();
    }

    public function update(Product $product): void
    {
    }

    public function delete(Id $id): void
    {
        foreach (self::$arProducts as $key => $product) {
            if ($product->getId()->equalTo($id)) {
                unset(self::$arProducts[$key]);
            }
        }
    }
}
