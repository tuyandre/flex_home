<?php

namespace Botble\Media\Repositories\Caches;

use Botble\Media\Repositories\Interfaces\MediaFolderInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class MediaFolderCacheDecorator extends CacheAbstractDecorator implements MediaFolderInterface
{
    public function getFolderByParentId(int|string|null $folderId, array $params = [], bool $withTrash = false)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function createSlug(string $name, int|string|null $parentId)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function createName(string $name, int|string|null $parentId)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function getBreadcrumbs(int|string|null $parentId, array $breadcrumbs = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getTrashed(int|string|null $parentId, array $params = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function deleteFolder(int|string|null $folderId, bool $force = false)
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function getAllChildFolders(int|string|null $parentId, array $child = [])
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function getFullPath(int|string|null $folderId, string|null $path = '')
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }

    public function restoreFolder(int|string|null $folderId)
    {
        $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }

    public function emptyTrash(): bool
    {
        return $this->flushCacheAndUpdateData(__FUNCTION__, func_get_args());
    }
}
