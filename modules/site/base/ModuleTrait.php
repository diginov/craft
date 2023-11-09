<?php

namespace modules\site\base;

use modules\site\services\AssetService;
use modules\site\services\BuildService;
use modules\site\services\CacheService;
use modules\site\services\EntryService;
use modules\site\services\GqlService;
use modules\site\services\MetaService;
use modules\site\services\SectionService;
use modules\site\services\UserService;

/**
 * @property-read AssetService $asset
 * @property-read BuildService $build
 * @property-read CacheService $cache
 * @property-read EntryService $entry
 * @property-read GqlService $gql
 * @property-read MetaService $meta
 * @property-read SectionService $section
 * @property-read UserService $user
 */
trait ModuleTrait
{
    // Public Methods
    // =========================================================================

    /**
     * Returns the asset service.
     *
     * @return AssetService
     */
    public function getAsset(): AssetService
    {
        return $this->get('asset');
    }

    /**
     * Returns the build service.
     *
     * @return BuildService
     */
    public function getBuild(): BuildService
    {
        return $this->get('build');
    }

    /**
     * Returns the cache service.
     *
     * @return CacheService
     */
    public function getCache(): CacheService
    {
        return $this->get('cache');
    }

    /**
     * Returns the entry service.
     *
     * @return EntryService
     */
    public function getEntry(): EntryService
    {
        return $this->get('entry');
    }

    /**
     * Returns the meta service.
     *
     * @return MetaService
     */
    public function getMeta(): MetaService
    {
        return $this->get('meta');
    }

    /**
     * Returns the gql service.
     *
     * @return GqlService
     */
    public function getGql(): GqlService
    {
        return $this->get('gql');
    }

    /**
     * Returns the section service.
     *
     * @return SectionService
     */
    public function getSection(): SectionService
    {
        return $this->get('section');
    }

    /**
     * Returns the user service.
     *
     * @return UserService
     */
    public function getUser(): UserService
    {
        return $this->get('user');
    }

    // Private Methods
    // =========================================================================

    /**
     * Sets the components of the module.
     */
    private function _setModuleComponents(): void
    {
        $this->setComponents([
            'asset' => AssetService::class,
            'build' => BuildService::class,
            'cache' => CacheService::class,
            'entry' => EntryService::class,
            'meta' => MetaService::class,
            'gql' => GqlService::class,
            'section' => SectionService::class,
            'user' => UserService::class,
        ]);
    }
}
