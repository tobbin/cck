<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $model = new Nisanth_Model_Color();
        $this->view->color = $model->getRandomHtmlColor();
    }

}



