<?php
namespace Epl2Label;

use PHPUnit\Framework\TestCase;

final class OutletTest extends TestCase
{
    public function test_getCommand()
    {
        $o = new Outlet('SKU', 7594, 'OBSERVACIONES');

        $this->assertArraySubset(
            array(
                'N',
                // lineas horizontales
                'LO20,0,730,5',
                'LO20,210,730,5',
                // lineas verticales
                'LO20,0,5,210',
                'LO745,0,5,210',
                'LO270,0,5,210',
                'LO350,0,5,210',
                // datos
                'A35,93,0,4,1,1,N,"SKU"',
                'A285,93,0,4,1,1,N,"7594"',
                'A365,94,0,3,1,1,N,"OBSERVACIONES"',

                'P1,1',
            ),
            $o->getCommand()
        );
    }

    public function test_no_copies()
    {
        $o = new Outlet('A', 1, 'B', 0);

        $this->assertTrue(
            empty($o->getCommand())
        );
    }
}
