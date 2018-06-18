<?php
/**
 * 配置helper类
 */

namespace Vendor\Fundation;

class Config {
    /**
     * @var array 配置分配文件注册路径
     */
    private static $configCategoryPath = [];

    /**
     * @var array 已加载的配置
     */
    private static $loadedConfig = [];

    /**
     * 注册配置文件信息
     *
     * @param array $configCategory 配置分类
     * @param array $configFilePath 该配置文件路径
     */
    public static function register($configCategory, $configFilePath) {
        if (!isset(self::$configCategoryPath[$configCategory])) {
            self::$configCategoryPath[$configCategory] = $configFilePath;
        }
    }

    /**
     * 加载配置文件
     *
     * @param string $configCategory
     */
    private static function load($configCategory) {
        if (isset(self::$configCategoryPath[$configCategory])) {
            self::$loadedConfig[$configCategory] = require self::$configCategoryPath[$configCategory];
        } else {
            throw new \Exception('未注册配置分类'.$configCategory);
        }
    }

    /**
     * 获取配置
     *
     * @param string $configCategory 配置分类
     * @param string $configName 配置名称,层级用.表示
     * @param string $defaultValue 配置找不到事用默认值
     * @return mixed|string
     */
    public static function get($configCategory, $configName = '', $defaultValue = '') {
        if (!isset(self::$loadedConfig[$configCategory])) {
            self::load($configCategory);
        }

        if (empty($configName)) {
            return self::$loadedConfig[$configCategory];
        }

        //拆解配置名称
        $configNameSections = explode('.', $configName);

        //定义当前匹配的config值
        $filteredConfig = self::$loadedConfig[$configCategory];

        //根据拆解后的配置片段查找最终的匹配项,找不到返回默认值
        foreach($configNameSections as $section) {
            if (is_array($filteredConfig) && isset($filteredConfig[$section])) {
                $filteredConfig = $filteredConfig[$section];
            } else {
                return $defaultValue;
            }
        }

        return $filteredConfig;
    }

}