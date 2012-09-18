<?php

namespace Ebutik\GitternBundle\Profiler;

use Gittern\Transport\TransportInterface;
use Gittern\Transport\RawObject;
use Symfony\Component\HttpKernel\Debug\Stopwatch;
use Ebutik\GitternBundle\Tools\StopwatchableTrait;

/**
* @author Magnus Nordlander
*/
class ProfilingTransportDecorator implements TransportInterface
{
    protected $stopwatch = null;

    protected $decoratee;
    protected $transport_logger;

    public function __construct(TransportInterface $decoratee, Stopwatch $stopwatch = null)
    {
        $this->decoratee = $decoratee;
        $this->setStopwatch($stopwatch);
    }

    public function setTransportLogger(TransportLogger $logger)
    {
        $this->transport_logger = $logger;
    }

    protected function performAction($stopwatch_section_name, $action_callable, $profile_callable)
    {
        $time_spent = null;
        $e = null;
        $start_time = null;

        $this->usingStopwatch(function(Stopwatch $stopwatch) use (&$e, $stopwatch_section_name) {
            $e = $stopwatch->start($stopwatch_section_name, 'gittern');
        });

        if (!$e)
        {
            $start_time = microtime(true);
        }

        $ret = $action_callable();

        if ($e) {
            $e->stop();
            $time_spent = $e->getTotalTime();
        }
        else
        {
            $time_spent = (microtime(true) - $start_time)*1000;
        }

        if ($this->transport_logger)
        {
            $profile_callable($this->transport_logger, $ret, $time_spent);
        }


        return $ret;
    }

    public function fetchRawObject($sha)
    {
        return $this->performAction(sprintf('gittern.fetchRawObject (%s)', $sha),
            function() use ($sha) {
                return $this->decoratee->fetchRawObject($sha);
            },
            function($logger, $ret, $time_spent) use ($sha)
            {
                $logger->addRawObjectFetch($sha, $ret, $time_spent);
            }
        );
    }

    public function putRawObject(RawObject $raw_object)
    {
        return $this->performAction(sprintf('gittern.putRawObject (%s)', $raw_object->getSha()),
            function() use ($raw_object) {
                return $this->decoratee->putRawObject($raw_object);
            },
            function($logger, $ret, $time_spent) use ($raw_object)
            {
                $logger->addRawObjectPut($raw_object, $time_spent);
            }
        );
    }

    public function resolveTreeish($treeish)
    {
        return $this->performAction(sprintf('gittern.resolveTreeish (%s)', $treeish),
            function() use ($treeish) {
                return $this->decoratee->resolveTreeish($treeish);
            },
            function($logger, $ret, $time_spent) use ($treeish)
            {
                $logger->addTreeishResolution($treeish, $ret, $time_spent);
            }
        );
    }

    public function resolveHead($head_name)
    {
        return $this->performAction(sprintf('gittern.resolveHead (%s)', $head_name),
            function() use ($head_name) {
                return $this->decoratee->resolveHead($head_name);
            },
            function($logger, $ret, $time_spent)
            {
            }
        );
    }

    public function setBranch($branch, $sha)
    {
        return $this->performAction(sprintf('gittern.setBranch (%s, %s)', $branch, $sha),
            function() use ($branch, $sha) {
                return $this->decoratee->setBranch($branch, $sha);
            },
            function($logger, $ret, $time_spent)
            {
            }
        );
    }

    public function removeBranch($branch)
    {
        return $this->performAction(sprintf('gittern.removeBranch (%s)', $branch),
            function() use ($branch) {
                return $this->decoratee->removeBranch($branch);
            },
            function($logger, $ret, $time_spent)
            {
            }
        );
    }

    public function hasIndexData()
    {
        return $this->performAction('gittern.hasIndexData',
            function() {
                return $this->decoratee->hasIndexData();
            },
            function($logger, $ret, $time_spent)
            {
            }
        );
    }

    public function getIndexData()
    {
        return $this->performAction('gittern.getIndexData',
            function() {
                return $this->decoratee->getIndexData();
            },
            function($logger, $ret, $time_spent)
            {
                $logger->addIndexDataGet(strlen($ret), $time_spent);
            }
        );
    }

    public function putIndexData($data)
    {
        return $this->performAction('gittern.putIndexData',
            function() use ($data) {
                return $this->decoratee->putIndexData($data);
            },
            function($logger, $ret, $time_spent) use ($data)
            {
                $logger->addIndexDataPut(strlen($data), $time_spent);
            }
        );
    }

    public function setStopwatch(Stopwatch $stopwatch = null)
    {
        $this->stopwatch = $stopwatch;
    }

    protected function usingStopwatch($callable)
    {
        if ($this->stopwatch)
        {
            $callable($this->stopwatch);
        }
    }
}