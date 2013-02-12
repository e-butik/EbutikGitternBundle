<?php

namespace Ebutik\GitternBundle\Cache;

use Gittern\Transport\TransportInterface;
use Gittern\Transport\RawObject;
use Doctrine\Common\Cache\Cache;

/**
* @author Magnus Nordlander
*/
class CachingTransportDecorator implements TransportInterface
{
    protected $decoratee;
    protected $cache;
    protected $ttl;

    public function __construct(TransportInterface $decoratee, Cache $cache, $ttl = 3600)
    {
        $this->decoratee = $decoratee;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    public function fetchRawObject($sha)
    {
        if ($this->cache->contains($sha))
        {
            return $this->cache->fetch($sha);
        }

        $raw_object = $this->decoratee->fetchRawObject($sha);

        if ($raw_object)
        {
            $this->cache->save($sha, $raw_object, $this->ttl);
        }

        return $raw_object;
    }

    public function putRawObject(RawObject $raw_object)
    {
        return $this->decoratee->putRawObject($raw_object);
    }

    public function resolveTreeish($treeish)
    {
        return $this->decoratee->resolveTreeish($treeish);
    }

    public function resolveHead($head_name)
    {
        return $this->decoratee->resolveHead($head_name);
    }

    public function setBranch($branch, $sha)
    {
        return $this->decoratee->setBranch($branch, $sha);
    }

    public function removeBranch($branch)
    {
        return $this->decoratee->removeBranch($branch);
    }

    public function hasIndexData()
    {
        return $this->decoratee->hasIndexData();
    }

    public function getIndexData()
    {
        return $this->decoratee->getIndexData();
    }

    public function putIndexData($data)
    {
        return $this->decoratee->putIndexData($data);
    }

    public function resolveTag($tag)
    {
        return $this->decoratee->resolveTag($data);
    }

    public function hasTag($tag)
    {
        return $this->decoratee->hasTag($data);
    }
}