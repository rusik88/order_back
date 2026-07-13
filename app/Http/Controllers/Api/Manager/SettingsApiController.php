<?php
namespace App\Http\Controllers\Api\Manager;

use App\Http\Controllers\Api\AbstractApiController;
use App\Http\Requests\Api\Manager\Setting\UpdateSettingRequest;
use App\Services\SettingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingsApiController extends AbstractApiController
{
    use ApiResponseTrait;

    public function __construct(private readonly SettingService $settingService) {
        parent::__construct();
    }

    public function get(Request $request, string $key): JsonResponse
    {
        if(!$this->hasAccess($request, 'setting:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            $setting = $this->settingService->get($key);

            if(!$setting) return $this->error('Setting not found', Response::HTTP_NOT_FOUND);

            return $this->success([
                'setting' => [
                    'key'   => $key,
                    'value' => $setting,
                ],
            ]);
        } catch(\Exception $err) {
            $this->log($request, "Setting", "get", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function all(Request $request): JsonResponse
    {
        if(!$this->hasAccess($request, 'setting:read')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            $settings = $this->settingService->all();

            if(!$settings) return $this->error('Settings not found', Response::HTTP_NOT_FOUND);

            return $this->success([
                'settings' => $settings,
            ]);
        } catch(\Exception $err) {
            $this->log($request, "Setting", "all", $err);
            return $this->error($err->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function update(UpdateSettingRequest $request): JsonResponse
    {
        if(!$this->hasAccess($request, 'setting:update')) {
            return $this->error(
                'Access denied.',
                403
            );
        }

        try {
            $settings = $request->settings;
            if(!empty($settings)) {
                $this->settingService->setAll($settings);
            }
        } catch(\Exception $err) {
            $this->log($request, "Setting", "update", $err);
            return $this->error($err->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->success([], 'Settings updated successfully');
    }
}
