<?php

namespace Miracuthbert\Royalty\Tests\Points\Actions;

use Miracuthbert\Royalty\Actions\ActionAbstract;

class CompletedTask extends ActionAbstract
{
    /**
     * Set the action key.
     *
     * @return mixed
     */
    public function key()
    {
        return 'completed-task';
    }
}
