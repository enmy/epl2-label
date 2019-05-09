<?php
namespace Epl2Label;

use EPL2\CommandCollection;
use EPL2\CommandInterface;
use EPL2\LineDrawBlack;
use EPL2\Point;
use EPL2\TextLines;

class Outlet implements CommandInterface
{
    /** @var int El numero de copias que se desea imprimir */
    protected $copies;

    /** @var string */
    protected $sku;

    /** @var int */
    protected $registro;

    /** @var string */
    protected $observacion;

    protected $config = array(
        'border' => array(
            'top' => 0,
            'right' => 730,
            'bottom' => 210,
            'left' => 20,
        ),
        'table' => array(
            'center_left_x' => 270,
            'center_right_x' => 350,
            'width' => 5,
        ),
        'font' => array(
            'sku' => 4,
            'registro' => 4,
            'observacion' => 3,
        ),
    );

    protected $commands;

    public function __construct($sku, $registro, $observacion, $copies = 1)
    {
        $this->sku = $sku;

        $this->registro = $registro;

        $this->observacion = $observacion;

        $this->copies = $copies;
    }

    public function getCommand()
    {
        if ($this->copies <= 0) {
            return array();
        }

        $this->setCommands();

        $return = $this->commands->getCommand();

        // Añade al inicio
        array_unshift($return, 'N');

        $return[] = 'P' . $this->copies . ',1';

        return $return;
    }

    protected function setCommands()
    {
        $this->commands = new CommandCollection;

        $this->addTableLines()
            ->addData();
    }

    /**
     * Realiza un dibujo parecido al siguiente:
     *  ________
     * |__|__|__|
     *
     */
    protected function addTableLines()
    {
        // top
        $this->commands->add(
            new LineDrawBlack(
                new Point(
                    $this->config['border']['left'],
                    $this->config['border']['top']
                ),
                $this->config['table']['width'],
                $this->config['border']['right']
            )
        );

        // bottom
        $this->commands->add(
            new LineDrawBlack(
                new Point(
                    $this->config['border']['left'],
                    $this->config['border']['bottom']
                ),
                $this->config['table']['width'],
                $this->config['border']['right']
            )
        );

        // left
        $this->commands->add(
            new LineDrawBlack(
                new Point(
                    $this->config['border']['left'],
                    $this->config['border']['top']
                ),
                $this->config['table']['width'],
                $this->config['border']['bottom'],
                LineDrawBlack::VERTICAL
            )
        );

        // right
        $this->commands->add(
            new LineDrawBlack(
                new Point(
                    $this->config['border']['right'] + $this->config['border']['left'] - $this->config['table']['width'],
                    $this->config['border']['top']
                ),
                $this->config['table']['width'],
                $this->config['border']['bottom'],
                LineDrawBlack::VERTICAL
            )
        );

        // center left
        $this->commands->add(
            new LineDrawBlack(
                new Point(
                    $this->config['table']['center_left_x'],
                    $this->config['border']['top']
                ),
                $this->config['table']['width'],
                $this->config['border']['bottom'],
                LineDrawBlack::VERTICAL
            )
        );

        // center right
        $this->commands->add(
            new LineDrawBlack(
                new Point(
                    $this->config['table']['center_right_x'],
                    $this->config['border']['top']
                ),
                $this->config['table']['width'],
                $this->config['border']['bottom'],
                LineDrawBlack::VERTICAL
            )
        );

        return $this;
    }

    protected function addData()
    {
        $this->addSku()
            ->addRegistro()
            ->addObservacion();

        return $this;
    }

    protected function addSku()
    {
        $sku = new TextLines(
            new Point(0, 0), // set later
            $this->config['font']['sku'],
            $this->sku,
            $this->config['table']['center_left_x']
        );

        $sku->setStartPosition(
            $this->calculatePositionForSku(
                $sku->getHeight()
            )
        );

        $this->commands->add($sku);

        return $this;
    }

    protected function addRegistro()
    {
        $registro = new TextLines(
            new Point(0, 0), // set later
            $this->config['font']['registro'],
            $this->registro,
            $this->config['table']['center_right_x'] - $this->config['table']['center_left_x']
        );

        $registro->setStartPosition(
            $this->calculatePositionForRegistro(
                $registro->getHeight()
            )
        );

        $this->commands->add($registro);

        return $this;
    }

    protected function addObservacion()
    {
        $observacion = new TextLines(
            new Point(0, 0), // set later
            $this->config['font']['observacion'],
            $this->observacion,
            $this->config['border']['right'] - $this->config['table']['center_right_x']
        );

        $observacion->setStartPosition(
            $this->calculatePositionForObservacion(
                $observacion->getHeight()
            )
        );

        $this->commands->add($observacion);

        return $this;
    }

    /**
     * Punto a la izquierta y centrado verticalmente.
     *
     * @param int $height Altura en puntos del texto. Esta altura depende del numero de lineas y el tamaño de letra
     * @return EPL2\Point
     */
    protected function calculatePositionForSku($height)
    {
        $font_sizes = TextLines::getFontSizes();

        // TODO: no toma en cuenta el multiplicador
        return new Point(
            $this->config['border']['left'] + $this->config['table']['width'] + 10,
            $this->calculateCenterVerticalAlign($height)
        );
    }

    protected function calculatePositionForRegistro($height)
    {
        $font_sizes = TextLines::getFontSizes();

        return new Point(
            $this->config['table']['center_left_x'] + $this->config['table']['width'] + 10,
            $this->calculateCenterVerticalAlign($height)
        );
    }

    protected function calculatePositionForObservacion($height)
    {
        $font_sizes = TextLines::getFontSizes();

        return new Point(
            $this->config['table']['center_right_x'] + $this->config['table']['width'] + 10,
            $this->calculateCenterVerticalAlign($height)
        );
    }

    /**
     * @param int $height Altura en puntos del texto. Esta altura depende del numero de lineas y el tamaño de letra
     * @return int
     */
    protected function calculateCenterVerticalAlign($height)
    {
        return (int) (($this->config['border']['top'] + $this->config['border']['bottom'] - $height) / 2);
    }
}
