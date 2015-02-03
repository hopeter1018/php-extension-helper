<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hopeter1018\ExtensionHelper;

/**
 * Description of Carbon
 *
 * @version $id$
 * @author peter.ho
 */
class Carbon
{

    /**
     * Loop thought the startDate - endDate and apply the looping date in Closure
     * 
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @param \Zms5\Helpers\Closure $func function ($indexDate)<br />{<br />}
     */
    public static function loop(Carbon $startDate, Carbon $endDate, \Closure $func)
    {
        $indexDate = $startDate->copy();
        while ($indexDate->lt($endDate)) {
            $func($indexDate->copy());
            $indexDate->addDay();
        }
    }

}
