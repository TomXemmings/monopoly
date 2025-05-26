<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use App\DTO\FuelStationModel;
use App\Enums\ServiceMethodEnum;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\DTO\FuelPriceModel;
use App\Enums\FuelTypeEnum;

class GasStationService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl      = config('services.e1.url');
        $this->clientId     = config('services.e1.client_id');
        $this->clientSecret = config('services.e1.client_secret');
    }

    /**
     * Получение токена авторизации
     *
     * @return string
     */
    protected function getAccessToken(): string
    {
        return Cache::remember('e1_token', 60 * 50, function () {
            $response = Http::asForm()->post("{$this->baseUrl}/connect/token", [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'scope'         => 'api',
            ]);

            if (!$response->successful()) {
                throw new HttpException($response->status(), 'Не удалось получить OAuth2 токен E1');
            }

            return $response->json()['access_token'];
        });
    }

    /**
     * Авторизация клиента
     *
     * @return PendingRequest
     */
    protected function authorizedClient(): PendingRequest
    {
        return Http::withToken($this->getAccessToken())
            ->acceptJson()
            ->baseUrl($this->baseUrl . '/api');
    }

    /**
     * Получение списка АЗС
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getStations(): Collection
    {
        $response = $this->authorizedClient()->get('/stations');

        if (!$response->successful()) {
            throw new HttpException($response->status(), 'Ошибка при получении станций из E1');
        }

        return collect($response->json())->map(function ($item) {
            return new FuelStationModel(
                id:             $item['id'],
                name:           $item['name'],
                brand:          $item['brand'] ?? 'Неизвестно',
                isAvailable:    $item['is_active'] ?? true,
                address:        $item['address'],
                longitude:      (float) $item['longitude'],
                lattitude:      (float) $item['latitude'],
                serviceMethods: [ServiceMethodEnum::Online],
            );
        });
    }

    /**
     * Получение цен на топливо по всем станциям
     *
     * @return Collection
     * @throws ConnectionException
     */
    public function getFuelPrices(): Collection
    {
        $response = $this->authorizedClient()->get('/stations/fuel_prices');

        if (!$response->successful()) {
            throw new HttpException($response->status(), 'Ошибка при получении цен на топливо');
        }

        return collect($response->json())->map(function ($item) {
            return new FuelPriceModel(
                stationId:     $item['station_id'],
                fuelType:      FuelTypeEnum::from($item['fuel_type']),
                fuelPrice:     (float) $item['price'],
                serviceMethod: ServiceMethodEnum::Online
            );
        });
    }

    /**
     * Получение подробной информации об АЗС по ID
     *
     * @param  string              $id
     * @return array
     * @throws ConnectionException
     */
    public function getStationDetails(string $id): array
    {
        $response = $this->authorizedClient()->get("/stations/{$id}");

        if (!$response->successful()) {
            throw new HttpException($response->status(), "Ошибка при получении станции {$id}");
        }

        return $response->json();
    }
}
