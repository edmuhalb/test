<?php

declare(strict_types=1);

namespace App\MkadDistance\Strategy;

use App\MkadDistance\Exception\DistanceException;
use App\MkadDistance\Exception\InnerPolygonException;
use App\MkadDistance\Geometry\DistanceBetweenPoints;
use App\MkadDistance\Geometry\Point;
use App\MkadDistance\Geometry\Polygon;
use InvalidArgumentException;
use Psr\SimpleCache\CacheException;
use Psr\SimpleCache\CacheInterface;
use Yandex\Geo\Api;
use Yandex\Geo\Exception as YandexGeoException;

class AddressDistanceCalculator extends PointDistanceCalculator
{
    private $api;

    public function __construct(
        string $yandexGeoCoderApiKey,
        Polygon $basePolygon,
        Polygon $junctionsPolygon,
        ?CacheInterface $cache = null,
        int $cacheTtl = 5 * 24 * 60 * 60
    ) {
        $this->api = new Api();
        $this->api->setToken($yandexGeoCoderApiKey);
        parent::__construct($basePolygon, $junctionsPolygon, $cache, $cacheTtl);
    }

    /**
     * @param string $target
     * @throws DistanceException
     * @throws InnerPolygonException
     * @throws InvalidArgumentException
     */
    public function calculate($target, bool $calcByRoutes = true): DistanceBetweenPoints
    {
        if (\is_string($target) === false) {
            throw new InvalidArgumentException('Target param most be string address');
        }
        $cacheKey = 'geocoder.' . md5(strtolower($target));
        try {
            if ($this->cache && $this->cache->has($cacheKey)) {
                $response = $this->cache->get($cacheKey);
            } else {
                $response = $this->api->setQuery($target)
                    ->setLimit(1)
                    ->load()
                    ->getResponse();

                if ($this->cache) {
                    $this->cache->set($cacheKey, $response, $this->cacheTtl);
                }
            }
        } catch (CacheException|YandexGeoException $e) {
            throw new DistanceException($e->getMessage(), $e->getCode(), $e);
        }

        if ($response && $response->getList()) {
            $target = Point::createFromArray([
                $response->getList()[0]->getData()['Latitude'],
                $response->getList()[0]->getData()['Longitude'],
            ]);
        } else {
            throw new DistanceException('No result from Yandex GeoCoder.');
        }
        return parent::calculate($target, $calcByRoutes);
    }
}
