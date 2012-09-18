<?php

namespace Ebutik\GitternBundle\Profiler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
* @author Magnus Nordlander
*/
class TransportDataCollector extends DataCollector
{
    protected $transport_decorator;

    public function __construct(TransportLogger $transport_logger)
    {
        $this->transport_logger = $transport_logger;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'raw_object_fetches' => $this->transport_logger->getRawObjectFetches(),
            'raw_object_puts' => $this->transport_logger->getRawObjectPuts(),
            'treeish_resolutions' => $this->transport_logger->getTreeishResolutions(),
            'index_data_gets' => $this->transport_logger->getIndexDataGets(),
            'index_data_puts' => $this->transport_logger->getIndexDataPuts(),
        );
    }

    public function getRawObjectFetches()
    {
        return $this->data['raw_object_fetches'];
    }

    public function getRawObjectPuts()
    {
        return $this->data['raw_object_puts'];
    }

    public function getTreeishResolutions()
    {
        return $this->data['treeish_resolutions'];
    }

    public function getIndexDataGets()
    {
        return $this->data['index_data_gets'];
    }

    public function getIndexDataPuts()
    {
        return $this->data['index_data_puts'];
    }

    public function getTotalRawObjectFetchTime()
    {
        return array_reduce($this->getRawObjectFetches(), function($result, $item) {
            return $result + $item['time'];
        }, 0);
    }

    public function getTotalRawObjectPutTime()
    {
        return array_reduce($this->getRawObjectPuts(), function($result, $item) {
            return $result + $item['time'];
        }, 0);
    }

    public function getTotalTreeishResolutionTime()
    {
        return array_reduce($this->getTreeishResolutions(), function($result, $item) {
            return $result + $item['time'];
        }, 0);
    }

    /**
     * Returns the collector name.
     *
     * @return string   The collector name.
     */
    public function getName()
    {
        return 'gittern_transport';
    }
}