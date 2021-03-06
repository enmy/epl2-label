<?php
namespace Epl2Label;

use EPL2\CommandCollection;
use EPL2\CommandInterface;
use EPL2\Point;
use EPL2\TextLines;

class DespachoTienda implements CommandInterface
{
    /** @var \EPL2\CommandCollection */
    private $commands;

    /** @var int El numero de copias que se desea imprimir */
    private $copies;

    /** @var string */
    private $id_pedido;

    /** @var string */
    private $id_referencia;

    /** @var int Puntero de linea */
    private $line;

    /** @var string */
    private $nombre;

    /** @var string */
    private $rut;

    /** @var string */
    private $tienda;

    private $config = [
        'width' => 790,
        'margin' => [
            'left' => 25,
            'top' => 5,
        ],
        'font' => [
            'l' => 4,
            'xl' => 5,
        ],
    ];

    public function __construct($rut, $nombre, $tienda, $id_pedido, $id_referencia, $copies = 1)
    {
        $this->rut = $rut;
        $this->nombre = $nombre;
        $this->tienda = $tienda;
        $this->id_pedido = $id_pedido;
        $this->id_referencia = $id_referencia;
        $this->copies = $copies;
    }

    public function getCommand()
    {
        if ($this->copies <= 0) {
            return [];
        }

        $this->setCommands();

        $return = $this->commands->getCommand();

        // Añade al inicio
        array_unshift($return, 'N');

        $return[] = "P{$this->copies}";

        return $return;
    }

    protected function setCommands()
    {
        $this->commands = new CommandCollection;
        $this->line = 0;
        $this->addData();
    }

    protected function addData()
    {
        return $this->addRut()
            ->addNombre()
            ->addTienda()
            ->addPedido()
            ->addReferecia();
    }

    protected function addRut()
    {
        $this->commands->add(
            $text = new TextLines(
                new Point($this->config['margin']['left'], $this->config['margin']['top'] + (60 * $this->line)),
                $this->config['font']['xl'],
                $this->rut,
                $this->config['width']
            )
        );
        $this->line += count($text->getCommand());
        return $this;
    }

    protected function addNombre()
    {
        $this->commands->add(
            $text = new TextLines(
                new Point($this->config['margin']['left'], $this->config['margin']['top'] + (60 * $this->line)),
                $this->config['font']['xl'],
                $this->nombre,
                $this->config['width']
            )
        );
        $this->line += count($text->getCommand());
        return $this;
    }

    protected function addTienda()
    {
        $this->commands->add(
            $text = new TextLines(
                new Point($this->config['margin']['left'], $this->config['margin']['top'] + (60 * $this->line)),
                $this->config['font']['xl'],
                $this->tienda,
                $this->config['width']
            )
        );
        $this->line += count($text->getCommand());
        return $this;
    }

    protected function addPedido()
    {
        $this->commands->add(
            $text = new TextLines(
                new Point($this->config['margin']['left'], $this->config['margin']['top'] + (60 * $this->line)),
                $this->config['font']['xl'],
                "WEB-{$this->id_pedido}",
                $this->config['width']
            )
        );
        $this->line += count($text->getCommand());
        return $this;
    }

    protected function addReferecia()
    {
        $this->commands->add(
            $text = new TextLines(
                new Point($this->config['margin']['left'], $this->config['margin']['top'] + (60 * $this->line)),
                $this->config['font']['l'],
                "REF-{$this->id_referencia}",
                $this->config['width']
            )
        );
        $this->line += count($text->getCommand());
        return $this;
    }
}
