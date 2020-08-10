<?php

namespace Gitter\Command;

use Gitter\GitterFactory;
use Symfony\Component\Console\Command\Command;

class AbstractCommand extends Command
{
    protected const CODE_SUCCESS = 0;
    protected const CODE_ERROR = 1;

    /**
     * @var GitterFactory|null
     */
    protected $factory;

    /**
     * @param GitterFactory $factory
     *
     * @return void
     */
    public function setFactory(GitterFactory $factory): void
    {
        $this->factory = $factory;
    }

    /**
     * @return \Gitter\GitterFactory
     */
    protected function getFactory(): GitterFactory
    {
        if ($this->factory === null) {
            $this->factory = new GitterFactory();
        }

        return $this->factory;
    }
}
