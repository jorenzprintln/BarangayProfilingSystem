<?php


class BaseController
{
    protected function render($view, $data = [])
    {
        extract($data);
        require_once VIEW_PATH . $view . '.php';
    }
}
