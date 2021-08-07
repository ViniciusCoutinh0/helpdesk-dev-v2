<?php

namespace App\Common;

use League\Plates\Engine;

class View
{
    private $view;

    public function __construct()
    {
        $this->view = Engine::create(__DIR__ . '/../../resources/views', 'php');
    }

    /**
     * Adiciona dados na página rederizada.
     *
     * @param array $data
     * @return array
     */
    public function addData(array $data)
    {
        return $this->view->addData($data);
    }

    /**
     * Renderiza a view.
     * @param $template [View name]
     * @param $data [Columns param]
     * @return string
    */
    public function render(string $tplname, array $data = []): string
    {
        return $this->view->render($tplname, $data);
    }

    /**
     * Retorna a instancia do Compomente
     *
     * @return Engine
     */
    public function engine(): Engine
    {
        return $this->view;
    }
}
