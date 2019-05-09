<?php
namespace Epl2Label;

use PHPUnit\Framework\TestCase;

final class DespachoTiendaTest extends TestCase
{
    public function test_getCommand()
    {
        $l = new DespachoTienda('11.111.111-1', 'NOMBRE APELLIDO', 'TIENDA', 123, '456');

        $this->assertArraySubset(
            [
                'N',
                'A25,5,0,5,1,1,N,"11.111.111-1"',
                'A25,65,0,5,1,1,N,"NOMBRE APELLIDO"',
                'A25,125,0,5,1,1,N,"TIENDA"',
                'A25,185,0,5,1,1,N,"WEB-123"',
                'A25,245,0,4,1,1,N,"REF-456"',
                'P1',
            ],
            $l->getCommand()
        );
    }

    public function test_multilinea()
    {
        $l = new DespachoTienda('11.111.111-1', 'NOMBRE APELLIDO', 'TIENDA MALL PARQUE PLAZA', 123, '456');

        $this->assertArraySubset(
            [
                'N',
                'A25,5,0,5,1,1,N,"11.111.111-1"',
                'A25,65,0,5,1,1,N,"NOMBRE APELLIDO"',
                'A25,125,0,5,1,1,N,"TIENDA MALL PARQUE"',
                'A25,179,0,5,1,1,N,"PLAZA"',
                'A25,245,0,5,1,1,N,"WEB-123"',
                'A25,305,0,4,1,1,N,"REF-456"',
                'P1',
            ],
            $l->getCommand()
        );
    }
}
