<?php

namespace app\model;

use think\facade\Cache;
use think\model\concern\SoftDelete;

/**
 * 配置模型
 */
class Config extends Base
{
    use SoftDelete;

    public const CACHE_PREFIX = 'config:';

    // 关键字搜索主键字段
    protected $keyword_fields = ['code','title','value'];
    public function searchCodeAttr($query, $value, $data)
    {
        $value && $query->where('code', 'like', '%' . $value . '%');
    }
    public function searchTitleAttr($query, $value, $data)
    {
        $value && $query->where('title', 'like', '%' . $value . '%');
    }
    public function searchValueAttr($query, $value, $data)
    {
        $value && $query->where('value', 'like', '%' . $value . '%');
    }

    // 值字段获取器及修改器
    public function getValueAttr($value)
    {
        return unserialize($value);
    }
    public function setValueAttr($value)
    {
        return serialize($value);
    }

    // Schema获取器
    public function getSchemaAttr($value, $data)
    {
        return [
            "x-decorator"   => "FormItem",
            "required"      => true,
            "type"          => "string",
            "title"         => $data['title'],
            "x-component"   => $data['component'],
            "description"   => $data['description'],
            "enum"          => $this->dict ? array_map(
                fn($item) => ['value' => $item['key_'], 'label' => $item['label']],
                $this->dict->items->visible(['key_', 'label'])->toArray()
            ) : []
        ];
    }

    // 获取缓存
    public static function fetchCache($code)
    {
        $cache = Cache::get(self::CACHE_PREFIX . $code);
        return is_null($cache) ? self::refreshCache($code) : $cache;
    }

    // 刷新缓存
    public static function refreshCache($code = null)
    {
        if (is_null($code)) {
            self::select()->each(function ($model) {
                Cache::set(self::CACHE_PREFIX . $model->code, $model->value);
            });
            return true;
        }

        $model = self::where('code', $code)->find();
        $cache = $model ? $model->value : '';
        Cache::set(self::CACHE_PREFIX . $code, $cache);
        return $cache;
    }

    // 刷新缓存
    public static function removeCache($code)
    {
        return Cache::delete(self::CACHE_PREFIX . $code);
        ;
    }

    // 模型事件
    public static function onAfterWrite($config)
    {
        self::refreshCache($config->code);
    }
    public static function onAfterDelete($config)
    {
        self::removeCache($config->code);
    }

    // 关联字典
    public function dict()
    {
        return $this->belongsTo(Dict::class, 'dict_key');
    }
}
