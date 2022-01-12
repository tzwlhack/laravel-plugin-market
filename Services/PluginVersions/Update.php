<?php
namespace Plugins\PluginMarket\Services\PluginVersions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Plugins\PluginMarket\DTOs\UpdatePluginVersionData;
use Plugins\PluginMarket\Enums\PluginVersionStatus;
use Plugins\PluginMarket\Models\MarketPluginVersion;

class Update
{
    public function execute(int $versionId,int $userId, bool $isAdmin,UpdatePluginVersionData $data): void
    {
        // 普通用户只能将插件状态改为待审核
        if (!$isAdmin && $data->status !== PluginVersionStatus::WAIT_PENDING) {
            throw new \Exception("无法修改当前插件状态！");
        }
        $mpv = MarketPluginVersion::query()
            ->when(!$isAdmin, fn (Builder $builder) =>
                $builder->notRelease()->whereHas('plugin', fn(Builder $builder) => $builder->where('author_id', $userId))
            )
            ->findOrFail($versionId);

        if (in_array($data->status, [PluginVersionStatus::ACTIVE, PluginVersionStatus::WAIT_PENDING])) {
            if (MarketPluginVersion::query()
                ->where('plugin_id', $mpv->plugin_id)
                ->where('version', $data->version)
                ->where('id', "<>", $mpv->id)
                ->release()
                ->exists()) {
                throw new \Exception("版本号已存在，请更换一个版本号！");
            }
        }

        $data->status !== null && $mpv->status = $data->status;
        $data->logo !== null && $mpv->logo = $data->logo;
        $data->version !== null && $mpv->version = $data->version;
        $mpv->price = $data->price->amountInCent;
        $data->description !== null && $mpv->description = $data->description;

        $mpv->saveOrFail();
    }
}