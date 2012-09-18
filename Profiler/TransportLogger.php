<?php

namespace Ebutik\GitternBundle\Profiler;

use Gittern\Transport\RawObject;

/**
* @author Magnus Nordlander
*/
class TransportLogger
{
    protected $raw_object_fetches = array();
    protected $treeish_resolutions = array();
    protected $raw_object_puts = array();
    protected $index_data_gets = array();
    protected $index_data_puts = array();

    public function addRawObjectFetch($sha, RawObject $raw_object = null, $time = null)
    {
        $this->raw_object_fetches[] = array(
            'sha' => $sha,
            'type' => ($raw_object ? $raw_object->getType() : null),
            'time' => $time,
        );
    }

    public function addTreeishResolution($treeish, $result, $time = null)
    {
        $this->treeish_resolutions[] = array(
            'treeish' => $treeish,
            'result' => $result,
            'time' => $time
        );
    }

    public function addRawObjectPut(RawObject $raw_object, $time)
    {
        $this->raw_object_puts[] = array(
            'sha' => $raw_object->getSha(),
            'type' => $raw_object->getType(),
            'length' => $raw_object->getLength(),
            'time' => $time,
        );
    }

    public function addIndexDataGet($length, $time)
    {
        $this->index_data_gets[] = array('length' => $length, 'time' => $time);
    }

    public function addIndexDataPut($length, $time)
    {
        $this->index_data_puts[] = array('length' => $length, 'time' => $time);
    }

    public function getRawObjectFetches()
    {
        return $this->raw_object_fetches;
    }

    public function getRawObjectPuts()
    {
        return $this->raw_object_puts;
    }

    public function getTreeishResolutions()
    {
        return $this->treeish_resolutions;
    }

    public function getIndexDataGets()
    {
        return $this->index_data_gets;
    }

    public function getIndexDataPuts()
    {
        return $this->index_data_puts;
    }
}