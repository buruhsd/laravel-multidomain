<?php

namespace Aljawad\Multidomain\Queue\Console;


use Aljawad\Multidomain\Queue\ListenerOptions;

class ListenCommand extends \Illuminate\Queue\Console\ListenCommand
{

    /**
     * Get the listener options for the command.
     *
     * @return \Illuminate\Queue\ListenerOptions
     */
    protected function gatherOptions()
    {
        return new ListenerOptions(
            $this->option('domain'),
            $this->option('env'), $this->option('delay'),
            $this->option('memory'), $this->option('timeout'),
            $this->option('sleep'), $this->option('tries'),
            $this->option('force')
        );
    }

}
